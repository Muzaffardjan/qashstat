<?php
namespace Feedback\ArrayObject;

class Feedback
{
	public $id;
	public $name;
	public $phone;
	public $email;
	public $subject;
	public $text;
	public $time;
	public $checked;

	public function exchangeArray($data)
	{
		$objectVars = $this->getArrayCopy();

		foreach($objectVars as $key => $value)
		{
			$this->{$key} = (isset($data[$key])) ? $data[$key] : $value;
		}
		if (!$this->time)
		{
			$this->time = time();
		}
		
		if(!$this->checked)
		{
			$this->checked = 0;
		}
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
}

?>