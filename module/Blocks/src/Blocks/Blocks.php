<?php  
namespace Blocks;

use Zend\ServiceManager\ServiceManager;

class Blocks
{
	const CONFIG_KEY = 'blocks';

	protected $storage;
	protected $service;

	public function __construct(Storage\Storage $storage, ServiceManager $service)
	{
		$this->service = $service;
		$this->setStorage($storage);
	}

	public function setStorage(Storage\Storage $storage)
	{
		$this->storage = $storage;
	}

	public function getStorage()
	{
		if($this->storage instanceof Storage\Storage)
		{
			return $this->storage;
		}

		throw new Exception\StorageNotSetException;
	}

	public function getBlockNames()
	{
		$config = $this->service->get('config');

		if(!isset($config[self::CONFIG_KEY]))
		{
			throw new Exception\ConfigNotFoundException;
		}

		return array_keys($config[self::CONFIG_KEY]);
	}

	public function setBlock($key, $locale, $content)
	{
		$config = $this->service->get('config');

		if(!isset($config[self::CONFIG_KEY][$key]))
		{
			throw new Exception\BlockNotExistsException;
		}

		return $this->getStorage()->setBlock($key, $locale, $content);
	}

	public function getBlock($key, $locale)
	{
		$config = $this->service->get('config');

		if(!isset($config[self::CONFIG_KEY][$key]))
		{
			return null;
		}

		return $this->getStorage()->getBlock($key, $locale);
	}
}
?>