<?php 
/**
 * Quiz module for Foreach.Soft Mod355 v2
 *
 * Abstract Answer class for extending
 * 
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://each.uz)
 * @author Kahramonov Javlonbek <kakjavlon@gmail.com>, 
 *		   Erkin Pardayev <erkin.pardayev@gmail.com>
 */ 
namespace Quiz\Questions;

use Application\Library\Stdlib;

abstract class AbstractAnswer implements AnswerInterface
{
	/**
	 * Exchanges array to object
	 * 
	 * @param array|Traversable $data
	 */
	public function exchangeArray($data)
	{
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
	 * Sets votes count 
	 *
	 * @param int $count Votes count to be set
	 */
	abstract public function setVotesCount($count);

	/**
	 * Gets votes count
	 *
	 * @return int
	 */
	abstract public function getVotesCount();
}
?>