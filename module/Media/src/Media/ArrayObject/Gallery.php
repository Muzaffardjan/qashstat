<?php  
namespace Media\ArrayObject;

class Gallery
{
	public $id;
	public $locale;
	public $url;
	public $title;
	public $images;

	public function exchangeArray($data)
	{
		if(!is_array($data) && !($data instanceof \Traversable))
			return;

		foreach($this->getData() as $property => $value)
		{
			if(isset($data[$property]))
			{
				$this->{$property} = $data[$property];
			}
		}
	}

	public function flush()
	{
		$arrayCopy = $this->getData();

		foreach($arrayCopy as $key => $value)
		{
			$this->{$key} = null;
		}
	}

	/**
	 * Exchanges array with cleaning
	 *
	 * @param array $array
	 * @return Media\ArrayObject\Gallery
	 */
	public function setData($array)
	{
		$this->flush();
		$this->exchangeArray($array);

		return $this;
	}

	public function getData()
	{
		return \Application\Library\Stdlib::get_public_vars($this);
	}
}
?>