<?php  
namespace Newsletter\Sender;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class Sender implements ServiceLocatorAwareInterface
{
    protected $services;

    protected $from;

    protected $mailTransport;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->services;
    }

    public function setMailTransport(TransportInterface $transport)
    {
    	$this->mailTransport = $transport;

    	return $this;
    }

    public function getMailTransport()
    {
    	if(null === $this->mailTransport) {
    		$config = $this->getServiceLocator()->get('config');

    		if(!isset($config['newsletter']['sender'])) {
    			throw new Exception\ConfigNotFoundException;
    		}

    		$senderConfig = $config['newsletter']['sender'];

    		if(!isset($senderConfig['transport'])) {
    			throw new Exception\InvalidConfigException;
    		}

    		$transportConfig = $senderConfig['transport'];

    		if(!isset($transportConfig['name']) || !class_exists($transportConfig['name'])) {
    			throw new Exception\TransportNotFoundException;
    		}

    		// Init transport class
    		$transport = new $transportConfig['name']();

    		// if there is options 
    		if(isset($transportConfig['options_class']) && isset($transportConfig['options'])) {
    			// Init options class with options 
    			$options = new $transportConfig['options_class']($transportConfig['options']);
    			
    			$transport->setOptions($options);
    		}

    		$this->mailTransport = $transport;
    	}

    	return $this->mailTransport;
    }

    public function setFrom($from)
    {
    	$this->from = $from;
    }

    public function getFrom()
    {
    	if(null === $this->from) {
    		$config = $this->getServiceLocator()->get('config');

    		if(!isset($config['newsletter']['sender'])) {
    			throw new Exception\ConfigNotFoundException;
    		}

    		$senderConfig = $config['newsletter']['sender'];

    		if(!isset($senderConfig['from'])) {
    			throw new Exception\InvalidConfigException;
    		}

    		$this->from = $senderConfig['from'];
    	}

    	return $this->from;
    }

    public function send($message)
    {
    	if(!(is_array($message) || $message instanceof Message)) {
    		throw new Exception\InvalidMessageException;
    	}

    	if(is_array($message)) {
    		$data		= $message;

    		if(!(isset($data['body']) && isset($data['subject']) && isset($data['to']))) {
    			throw new Exception\InvalidMessageException;
    		}

    		$message 	= new Message();

			$message->setBody($data['body']);
			$message->addTo($data['to']);
			$message->setSubject($data['subject']);
    	}


		$message->setFrom($this->getFrom());
		$this->getMailTransport()->send($message);
    }
}
?>