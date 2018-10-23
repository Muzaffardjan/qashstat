<?php  
namespace Menu\ArrayObject;

use Indexing\Index\IndexInterface;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;

class Menu
{
	public $id;
	public $name;
	public $structure;
	public $position;
	public $locale;

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
	 * @return Pages\ArrayObject\Page
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

	public function getConfig()
	{
		//return new \Zend\Navigation\Navigation();
		return \Zend\Json\Json::decode($this->structure, \Zend\Json\Json::TYPE_ARRAY);
	}
}
?>