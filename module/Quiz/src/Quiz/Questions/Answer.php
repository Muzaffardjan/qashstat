<?php  
namespace Quiz\Questions;

class Answer extends AbstractAnswer
{
	/**
	 * @var int|string $id Unique id of answer
	 */
	public $id;

	/**
	 * @var int|string $question Id of question
	 */
	public $question;

	/**
	 * @var string $text Answer
	 */
	public $text;

	/**
	 * @var int|string $voted Vote count of answer
	 */
	public $voted;

	/**
	 * @var int|string $order Order of answer in question
	 */
	public $index;

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return (int) $this->index;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getQuestionId()
	{
		return (int) $this->question;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVotesCount($count)
	{
		$this->voted = (int) $count;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVotesCount()
	{
		return (int) $this->voted;
	}
}
?>