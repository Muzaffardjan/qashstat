<?php  
namespace UsefulLinks\Links;

use Application\Library\Stdlib;

abstract class AbstractLink implements LinkInterface
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
	 * @return AbstractLink
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
	 * @return AbstractLink
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
	 * Gets array data of object
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->getData();
	}
}
?>