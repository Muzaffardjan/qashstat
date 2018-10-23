<?php 
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Request;

class NarratorController extends AbstractActionController
{
	protected $locale;
	protected $config;

	public function onDispatch(\Zend\Mvc\MvcEvent $e)
	{
		$this->locale = $this->params('locale');
		$globalConfig = $this->getServiceLocator()->get('config');
		$this->config = $globalConfig['narrator'];
		parent::onDispatch($e);
	}

	public function speakAction()
	{
		$referer = false;
		if ($this->getRequest()->getHeader('Referer'))
		{
			$referer = $this->getRequest()->getHeader('Referer')->uri()->toString();
		}

		if ( ! $referer || ! $this->getRequest()->isXmlHttpRequest() || ! $this->getRequest()->isPost())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$form = new \Application\Form\NarratorForm();
		$form->setData($this->getRequest()->getPost());

		if ($form->isValid())
		{
			$formData  = $form->getData();
			$csrfToken = $formData['csrf-token'];

			if (self::getCsrfToken($referer) != $csrfToken)
			{
				$this->getResponse()->setStatusCode(404);
				return;
			}

			$textToSpeak = $formData['text-to-speak'];

			$request = $this->createISpeechRequest($textToSpeak, $this->locale);

			$client = new \Zend\Http\Client();
			$client->setOptions($this->config['client_options']);
			$client->setRequest($request);
			$client->clearAuth();

			try
			{
				$response = $client->send();
				$statusCode = $response->getStatusCode();

				if (200 <= $statusCode && $statusCode < 300)
				{
					switch ($this->locale) {
						case 'en-US':
							$content = substr($response->getContent(), 0 , -8500);
							break;
						
						default:
							$content = $response->getContent();
							break;
					}

					$content = 'data:audio/mpeg;base64,' . base64_encode($content);

					return new \Zend\View\Model\JsonModel(array(
						'actionId' => $formData['action-id'],
						'data' => $content
					));
				}
				else throw new \Exception("Error Processing Request");
				
			}
			catch (\Exception $e)
			{
				var_dump($e); die();

				$messagesDir = $this->config['voice_messages']['directory'];
				$errorMessage = $messagesDir . sprintf($this->config['voice_messages']['name_template'], $this->locale);

				if (file_exists($errorMessage))
				{
					echo file_get_contents($errorMessage);
				}
			}

			exit();
		}

		return false;
	}

	protected function createIvonaPostRequest()
	{
		$textToSpeak = trim($this->getRequest()->getPost('text-to-speak'));
		$payload = \Zend\Json\Json::encode(array(
			'Input' => array(
				'Data' => $textToSpeak
			),
			// 'Voice' => array(
			// 	'Language' => 'tr',
			// 	'Gender' => 'Female'
			// )
		));

		$requestMethod = 'POST';
		$contentType = 'application/json';
		$requestTime = time();
		$requestTime = mktime(9, 20, 54, 9, 13, 2013);
		$amzDate   = date('Ymd\THis\Z', $requestTime);
		$dateStamp = date('Ymd', $requestTime);

		$payloadHash = hash('sha256', $payload);

		$canonicalHeaders  = 'content-type:' . $contentType . "\n";
		$canonicalHeaders .= 'host:' . $this->config['host'] . "\n";
		$canonicalHeaders .= 'x-amz-content-sha256:' . $payloadHash . "\n";
		$canonicalHeaders .= 'x-amz-date:' . $amzDate . "\n";

		$canonicalQueryString = '';

		$signedHeaders = 'content-type;host;x-amz-content-sha256;x-amz-date';

		$canonicalRequest  = $requestMethod . "\n";
		$canonicalRequest .= $this->config['canonical_uri'] . "\n";
		$canonicalRequest .= $canonicalQueryString . "\n";
		$canonicalRequest .= $canonicalHeaders . "\n";
		$canonicalRequest .= $signedHeaders . "\n";
		$canonicalRequest .= $payloadHash;

		$algorithm = 'AWS4-HMAC-SHA256';
		$credentialScope = $dateStamp . '/' . $this->config['region'] . '/tts/aws4_request';

		$stringToSign  = $algorithm . "\n";
		$stringToSign .= $amzDate . "\n";
		$stringToSign .= $credentialScope . "\n";
		$stringToSign .= hash('sha256', $canonicalRequest);

		$dateKey    = self::hmacSha256("AWS4" . $this->config['credentials']['secretKey'], $dateStamp, true);
		$regionKey  = self::hmacSha256($dateKey, $this->config['region'], true);
		$serviceKey = self::hmacSha256($regionKey, "tts", true);
		$signingKey = self::hmacSha256($serviceKey, "aws4_request", true);
		$signature  = self::hmacSha256($signingKey, $stringToSign);

		$authorizationHeader  = $algorithm . ' ';
		$authorizationHeader .= 'Credential=' . $this->config['credentials']['accessKey'] . '/';
		$authorizationHeader .= $credentialScope . ', ';
		$authorizationHeader .= 'SignedHeaders=' . $signedHeaders . ', ';
		$authorizationHeader .= 'Signature=' . $signature;

		$requestUri = new\Zend\Uri\Http();
		$requestUri->setScheme('http');
		$requestUri->setHost($this->config['host']);
		$requestUri->setPath($this->config['canonical_uri']);
		$requestUri->setPort(80);

		$request = new Request();
		$request->setMethod(Request::METHOD_POST);
		$request->setUri($requestUri);
		$request->getHeaders()->addHeaders(array(
			'Host' => $this->config['host'],
			'Content-Type' => 'application/json',
			'X-Amz-Date' => $amzDate,
			'Authorization' => $authorizationHeader,
			'x-amz-content-sha256' => $payloadHash,
			'Content-Length' => strlen($payload)
		));

		$request->setContent($payload);
		return $request;
	}

	protected function createIvonaGetRequest()
	{
		$textToSpeak = trim($this->getRequest()->getPost('text-to-speak'));
		$payload = '';

		$requestMethod = 'GET';
		$requestTime = time();
		$requestTime = mktime(9, 20, 54, 9, 13, 2013);
		$amzDate   = date('Ymd\THis\Z', $requestTime);
		$dateStamp = date('Ymd', $requestTime);

		$payloadHash = hash('sha256', $payload);

		$algorithm = 'AWS4-HMAC-SHA256';

		$signedHeaders = "host";
		$credentialScope = $dateStamp . '/' . $this->config['region'] . '/tts/aws4_request';

		$canonicalRequest  = $requestMethod . "\n";
		$canonicalRequest .= $this->config['canonical_uri'] . "\n";
		$canonicalRequest .= "Input.Data=" . rawurlencode($textToSpeak) . "&Input.Type=" . rawurlencode("text/plain") . '&';
		$canonicalRequest .= 'X-Amz-Algorithm=' . rawurlencode($algorithm) . '&';
		$canonicalRequest .= 'X-Amz-Credential=' . rawurlencode($this->config['credentials']['accessKey'] . '/' . $credentialScope) . '&';
		$canonicalRequest .= 'X-Amz-Date=' . rawurlencode($amzDate) . '&';
		$canonicalRequest .= 'X-Amz-SignedHeaders=' . rawurlencode($signedHeaders) . "\n";
		$canonicalRequest .= 'host:' . $this->config['host'] . "\n\n";
		$canonicalRequest .= 'host' . "\n" . $payloadHash;

		$stringToSign  = $algorithm . "\n";
		$stringToSign .= $amzDate . "\n";
		$stringToSign .= $credentialScope . "\n";
		$stringToSign .= hash('sha256', $canonicalRequest);

		$dateKey    = self::hmacSha256("AWS4" . $this->config['credentials']['secretKey'], $dateStamp, true);
		$regionKey  = self::hmacSha256($dateKey, $this->config['region'], true);
		$serviceKey = self::hmacSha256($regionKey, "tts", true);
		$signingKey = self::hmacSha256($serviceKey, "aws4_request", true);
		$signature  = self::hmacSha256($signingKey, $stringToSign);

		$authorizationHeader  = $algorithm . ' ';
		$authorizationHeader .= 'Credential=' . $this->config['credentials']['accessKey'] . '/';
		$authorizationHeader .= $credentialScope . ', ';
		$authorizationHeader .= 'SignedHeaders=' . $signedHeaders . ', ';
		$authorizationHeader .= 'Signature=' . $signature;

		$requestUri = new\Zend\Uri\Http();
		$requestUri->setScheme('https');
		$requestUri->setHost($this->config['host']);
		$requestUri->setPath($this->config['canonical_uri']);

		$requestUri->setQuery(array(
			'Input.Data' => rawurlencode($textToSpeak),
			'Input.Type' => rawurlencode('text/plain'),
			'X-Amz-Date' => rawurlencode($algorithm),
			'X-Amz-Credential' => rawurlencode($this->config['credentials']['accessKey'] . '/' . $credentialScope),
			'X-Amz-SignedHeaders' => 'host',
			'X-Amz-Signature' => $payloadHash
		));

		$request = new Request();
		$request->setMethod(Request::METHOD_GET);
		$request->setUri($requestUri);
		return $request;
	}

	protected function createISpeechRequest($textToSpeak, $locale)
	{
		$replacePairs = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
	        'г' => 'g',   'д' => 'd',   'е' => 'e',
	        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
	        'и' => 'i',   'й' => 'y',   'к' => 'k',
	        'л' => 'l',   'м' => 'm',   'н' => 'n',
	        'о' => 'o',   'п' => 'p',   'р' => 'r',
	        'с' => 's',   'т' => 't',   'у' => 'u',
	        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
	        'ч' => 'ç',   'ш' => 'ş',   'щ' => 'ş',
	        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
	        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
	        'ҳ' => 'h',   'ў' => 'ü',   'ғ' => 'ğ',
	        'қ' => 'k',

	        'А' => 'A',   'Б' => 'B',   'В' => 'V',
	        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
	        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
	        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
	        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
	        'О' => 'O',   'П' => 'P',   'Р' => 'R',
	        'С' => 'S',   'Т' => 'T',   'У' => 'U',
	        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
	        'Ч' => 'Ç',   'Ш' => 'Ş',   'Щ' => 'Ş',
	        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
	        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
	        'Ҳ' => 'H',   'ў' => 'Ü',   'Ғ' => 'Ğ',
	        'Қ' => 'K'
		);

		switch ($locale)
		{
			case 'en-US':
				$voice = 'usenglishmale';
				break;

			case 'uz-UZ':
				$textToSpeak = strtr($textToSpeak, array(
					"ch" => 'ç',   "Ch" => 'Ç',   "CH" => 'Ç',
					"sh" => 'ş',   "Sh" => 'Ş',   "SH" => 'Ş',
					"o'" => 'ü',   "O'" => 'Ü',
					"g'" => 'ğ',   "G'" => 'Ğ',
					"x"  => 'h',   "X"  => 'H',
					"q"  => 'k',   'Q'  => 'K'
				));
				$voice = 'eurturkishmale';
				break;

			case 'cy-UZ':
				$textToSpeak = strtr($textToSpeak, $replacePairs);
				$voice = 'eurturkishmale';
				break;

			case 'ru-RU':
				$voice = 'rurussianmale';
				break;
		}

		$requestUri = new\Zend\Uri\Http();
		$requestUri->setScheme('http')
				   ->setHost('ispeech.org')
				   ->setPath('/p/generic/getaudio')
				   ->setQuery(array(
				       'text' => $textToSpeak,
				       'voice' => $voice,
				       'speed' => 0,
				       'action' => 'convert'
				   ));

		$request = new Request();
		$request->setMethod(Request::METHOD_GET);
		$request->setUri($requestUri);

		return $request;
	}

	public static function hmacSha256($key, $msg, $rawOutput = false)
	{
		return hash_hmac('sha256', $msg, $key, $rawOutput);
	}

	public static function getCsrfToken($url)
	{
		$secretKey = self::hmacSha256('narrator', 'speak');
		
		return md5($url . $secretKey);
	}
}
?>