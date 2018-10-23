<?php
namespace Newsletter\ArrayObject;

class Newsletter
{
	public $email;
	public $link;
	public $id;

	public function exchangeArray($data)
	{
		$objectVars = $this->getArrayCopy();

		foreach($objectVars as $key => $value)
		{
			$this->{$key} = (isset($data[$key])) ? $data[$key] : $value;
		}
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
}

?>