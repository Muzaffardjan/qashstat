<?php  
namespace Users\Identity;

use Users\Authentication\ResultIdentityInterface;
use ZfcRbac\Identity\IdentityInterface;
use Users\ArrayObject\Control;
use Zend\Json\Json;

class User implements ResultIdentityInterface, IdentityInterface
{
	protected $arrayObject;

	public function __get($name)
	{
		$arrayObject = $this->getArrayObject();

		if (property_exists($arrayObject, $name)) {
			return $arrayObject->{$name};
		}

		return null;
	}

	public function getPassword()
	{
		return $this->getArrayObject()->password;
	}

	public function setArrayObject(Control $object)
	{
		$this->arrayObject = $object;

		return $this;
	}

	public function getArrayObject()
	{
		return $this->arrayObject;
	}

	public function getRoles()
	{
		if(!($arrayObject = $this->getArrayObject()))
			return null;

		return explode(',', $arrayObject->roles);
	}

	public function toString()
	{
		if (! ($arrayObject = $this->getArrayObject())) {
            throw new \Exception('ArrayObject is not set!');
        }

		return Json::encode($arrayObject->getData());
	}

	public function fromString($string)
	{
        /**
         * @var Control $arrayObject
         */
		$arrayObject = new Control();

		$arrayObject->exchangeArray(Json::decode($string, Json::TYPE_ARRAY));
		$this->setArrayObject($arrayObject);
	}
}
?>