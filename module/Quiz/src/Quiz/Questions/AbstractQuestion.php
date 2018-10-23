<?php 
/**
 * Quiz module for Foreach.Soft Mod355 v2
 *
 * Abstract Question class for extending
 * 
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://each.uz)
 * @author Kahramonov Javlonbek <kakjavlon@gmail.com>, 
 *		   Erkin Pardayev <erkin.pardayev@gmail.com>
 */ 
namespace Quiz\Questions;

use Application\Library\Stdlib;

abstract class AbstractQuestion implements QuestionInterface
{
	/**
	 * @var array
	 */
	protected $answers;

	/**
	 * @var int $voted All votes count
	 */
	protected $voted;

	/**
	 * @var bool $buffered Is statistics buffered state
	 */
	protected $buffered = false;

	/**
	 * Exchanges array to object
	 * 
	 * @param array|Traversable $data
	 */
	public function exchangeArray($data)
	{
		// Set buffer to false
		$this->buffered = false;

		foreach($data as $property => $item)
		{
			if(property_exists($this, $property))
			{
				$this->{$property} = $item;
			}
		}
	}

	/**
	 * Flushes public properties of object
	 *
	 * @return AbstractAnswer
	 */
	public function flush()
	{
		foreach($this->getData() as $property => $value)
		{
			$this->{$property} = null;
		}

		// Flush answers different question contains diffrent answers
		$this->answers = null;

		return $this;
	}

	/**
	 * Sets array data to object
	 * first flushes object public properties
	 *
	 * @param array|Traversable $data
	 * @return AbstractAnswer
	 */
	public function setData($data)
	{
		// Flushes object's public vars
		// Exchanges array
		$this->flush()->exchangeArray($data);

		return $this;
	}

	/**
	 * Gets array form of object
	 *
	 * @return array
	 */
	public function getData()
	{
		return Stdlib::get_public_vars($this);
	}

	/**
	 * Gets answer count
	 * 
	 * @return int
	 */
	public function countAnswers()
	{
		if(is_array($this->answers) || $this->answers instanceof \Traversable)
		{
			return count($this->answers);
		}

		return 0;
	}

	/**
	 * Adds answer to question
	 *
	 * @param Answer $answer
	 * @return AbstractQuestion
	 */
	public function addAnswer(Answer $answer)
	{
		// Set buffer to false
		$this->buffered = false;

		if(!$answer->getOrder() && $answer->getOrder() !== 0)
		{
			$this->answers[] = $answer;
		}
		else
		{
			$this->answers[$answer->getOrder()] = $answer;
		}

		return $this;
	}

	/**
	 * Adds answers to question
	 *
	 * @param array|Traversable $answers
	 * @throws Exception\InvalidArgumentException 
	 * 			if argument not array|Traversable 
	 *			or array contains non Answer items
	 * @return AbstractQuestion
	 */
	public function addAnswers($answers)
	{
		// Set buffer to false
		$this->buffered = false;

		if(!is_array($answers) && !$answers instanceof \Traversable)
		{
			throw new Exception\InvalidArgumentException('Argument must be array or Traversable');
		}

		foreach($answers as $answer)
		{
			if(!$answer instanceof Answer)
			{
				throw new Exception\InvalidArgumentException('Collection contains non Answer items');
			}

			if(!$answer->getOrder() && $answer->getOrder() !== 0)
			{
				$this->answers[] = $answer;
			}
			else
			{
				$this->answers[$answer->getOrder()] = $answer;
			}
		}

		return $this;
	}

	/**
	 * Sets answers to question
	 *
	 * @param array|Traversable $answers
	 * @throws Exception\InvalidArgumentException 
	 * 			if argument not array|Traversable 
	 *			or array contains non Answer items
	 * @return AbstractQuestion
	 */
	public function setAnswers($answers)
	{
		// Set buffer to false
		$this->buffered = false;

		if(!is_array($answers) && !$answers instanceof \Traversable)
		{
			throw new Exception\InvalidArgumentException('Argument must be array or Traversable');
		}

		// Flush answers
		$this->answers = array();

		foreach($answers as $answer)
		{
			if(!$answer instanceof Answer)
			{
				throw new Exception\InvalidArgumentException('Collection contains non Answer items');
			}

			if(!$answer->getOrder() && $answer->getOrder() !== 0)
			{
				$this->answers[] = $answer;
			}
			else
			{
				$this->answers[$answer->getOrder()] = $answer;
			}
		}

		return $this;
	}

	/**
	 * Gets all answers as array
	 *
	 * @return array
	 */
	public function getAnswers()
	{
		if(null === $this->answers)
		{
			// emtpty array if answer not set
			return array();
		}

		// Sort array by key because array keys represent order of answers
		ksort($this->answers);

		return $this->answers;
	}

	/**
	 * Gets votes count
	 *
	 * @return int
	 */
	public function getVotesCount()
	{
		if(null === $this->voted || !$this->buffered)
		{
			$this->voted 	= 0;
			$this->buffered = true;

			foreach($this->getAnswers() as $answer)
			{
				$this->voted += $answer->getVotesCount();
			}
		}

		return $this->voted;
	}
}
?>