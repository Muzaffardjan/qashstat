<?php  
namespace Blocks;

class Block
{
	public $name;

	public $locale;

	public $content;

	public function setData($data)
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

	public function getData()
	{
		return \Application\Library\Stdlib::get_public_vars($this);
	}
}
?>