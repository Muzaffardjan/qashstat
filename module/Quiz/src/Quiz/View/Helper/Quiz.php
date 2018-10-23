<?php  
namespace Quiz\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Quiz extends AbstractHelper
{
	/**
	 * Is voted for question
	 *
	 * @param int $id question id
	 * @return bool
	 */
	public function isVoted($id)
	{
		return $this->getQuiz()->isVoted($id);
	}

	/**
	 * Gets Quiz object
	 *
	 * @return Quiz\Quiz
	 */
	public function getQuiz()
	{
		return $this->getView()
				->getHelperPluginManager()
				->getServiceLocator()
				->get('Quiz\Quiz');
	}

	/**
	 * Gets Random question
	 * 
	 * @return Quiz\Questions\QuestionInterface|false
	 */
	public function getRandomQuestion()
	{
		return $this->getQuiz()
				->getRandomQuestion
				(
					$this->getView()
						->currentLocale(),
					true
				);
	}

	/**
	 * @param string $partial
	 * @return Quiz
	 */
	public function __invoke($partial = null)
	{
		if(null === $partial)
		{
			return $this;
		}

		return $this->getView()->partial(
			$partial, 
			array(
				'question' => $this->getRandomQuestion(),
			)
		);
	}
}
?>