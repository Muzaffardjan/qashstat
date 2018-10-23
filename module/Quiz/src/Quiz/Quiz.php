<?php
namespace Quiz;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Http\Header\SetCookie;
use Quiz\Questions\QuestionsProviderInterface;
use Quiz\Questions\AnswerInterface;

class Quiz implements ServiceLocatorAwareInterface
{
	const COOKIE_NAME = 'quiz';

	const COOKIE_PATH = '/';

	protected $services;

	protected $questionsProvider;

	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->services;
    }

	public function setQuestionsProvider(QuestionsProviderInterface $provider)
	{
		$this->questionsProvider = $provider;
	}

	public function getQuestionsProvider()
	{
		if(null === $this->questionsProvider)
		{
			$this->questionsProvider = $this->getServiceLocator()->get('Quiz\Questions\QuestionsProvider');
			
			$this->questionsProvider
			->setDbAdapter
			(
				$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
			);
		}

		return $this->questionsProvider;
	}

	/**
	 * Gets random question
	 * 
	 * @return Questions\QuestionInterface
	 */
	public function getRandomQuestion($locale = null, $mergeAnswers = true)
	{
		return $this->getQuestionsProvider()->getRandomQuestion($locale, $mergeAnswers);
	}

	/**
	 * Is user voted to question
	 *
	 * @param int $question Question id
	 * @return bool
	 */
	public function isVoted($question)
	{
		$cookie = $this->getCookie();

		if(is_object($cookie) && $cookie->offsetExists(self::COOKIE_NAME))
		{
			$voted_to = unserialize($cookie->offsetGet(self::COOKIE_NAME));

			if(is_array($voted_to) && array_search($question, $voted_to) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Increments vote count and writes user vote
	 *
	 * @throws Exception\InvalidArgumentException if param is invalid
	 * @param QuestionInterface|int $answer
	 */
	public function vote($answer)
	{
		if(!$answer instanceof AnswerInterface && (int) $answer <= 0)
		{
			throw new Exception\InvalidArgumentException(
				sprintf(
					'AnswerInterface or int required \'%s\' given',
					gettype($answer) == 'object'? get_class($answer): gettype($answer)
				)
			);
		}
		elseif(!$answer instanceof AnswerInterface && (int) $answer > 0)
		{
			$answer = $this->getQuestionsProvider()->getAnswer((int) $answer);

			if(!$answer)
			{
				throw new Exception\InvalidArgumentException('Invalid answer id given');
			}
		}

		if(!$this->isVoted($answer->getQuestionId()))
		{
			// Increment vote count
			$answer->setVotesCount($answer->getVotesCount() + 1);
			// Save it
			$this->getQuestionsProvider()->updateAnswer($answer);
			// Remember voter
			$this->rememberVoter($answer->getQuestionId());
		}
	}

	/**
	 * Remebers voter writes info to cookie
	 * 
	 * @param int $question Question id that user voted
	 */
	protected function rememberVoter($question)
	{
		if(!$this->isVoted($question))
		{
			$cookies 	= $this->getCookie();
			$quiz 		= array();

			if(is_object($cookies) && $cookies->offsetExists(self::COOKIE_NAME))
			{
				$quiz = unserialize($cookies->offsetGet(self::COOKIE_NAME));

				if(!is_array($quiz))
				{
					$quiz = array();
				}
			}

			if(array_search($question, $quiz) === false)
			{
				$quiz[] = $question;

				$this->setCookie(new SetCookie(self::COOKIE_NAME, serialize($quiz), time() + 365 * 60 * 60 * 24, self::COOKIE_PATH));
			}
		}
	}

	/**
	 * Sets cookie
	 *
	 * @param SetCookie $cookie
	 */
	protected function setCookie(SetCookie $cookie)
	{
		return $this->getServiceLocator()
		->get('response')
		->getHeaders()
		->addHeader($cookie);
	}

	/**
	 * Gets cookies
	 *
	 * @return Cookie
	 */
	protected function getCookie()
	{
		return $this->getServiceLocator()->get('request')->getCookie();
	}
}