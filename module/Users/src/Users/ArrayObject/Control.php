<?php  
namespace Users\ArrayObject;

class Control
{
	public $id;
	public $login;
	public $password;
	public $name;
	public $roles;
	public $description;

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
	 * Exchanges array
	 *
	 * @param array $array
	 * @return Users\ArrayObject\Control
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

	public function hashPassword()
	{
		$string 	= $this->password;
		$salted 	= md5($string . '666F72656163682E736F6674');
		$original 	= md5($string);
		$cropped 	= "";

		for($i=0; $i<16; $i+=2)
			$cropped .= $original[$i];

		$result = substr($salted, 0, 16). $cropped . substr($salted, 16,31);
		$this->password = $result;

		return $this;
	}
}
?>