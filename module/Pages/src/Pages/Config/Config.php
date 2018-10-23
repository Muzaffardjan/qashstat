<?php 
namespace Pages\Config;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Writer\PhpArray as Writer;

class Config
{
	const STORAGE_PATH = 'module/Pages/config/pages.config.php';

	protected $config;

	public function getConfig()
	{
		if(!$this->config instanceof ZendConfig)
		{
			if(!file_exists(self::STORAGE_PATH))
			{
				file_put_contents(self::STORAGE_PATH, '');
			}

			$this->config = new ZendConfig(include self::STORAGE_PATH);
		}

		return $this->config;
	}

	public function reloadConfig()
	{
		if(!file_exists(self::STORAGE_PATH))
		{
			file_put_contents(self::STORAGE_PATH, '');
		}

		$this->config = new ZendConfig(include self::STORAGE_PATH);

		return $this;
	}

	public function set($key, $value)
	{
		$config = new ZendConfig($this->getConfig()->toArray(), true);
		$writer = new Writer();

		$config->{$key} = $value;

		$writer->toFile(self::STORAGE_PATH, $config);
		return $this->reloadConfig();
	}	
}
?>