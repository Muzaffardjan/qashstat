<?php  
namespace Menu;

class Provider
{
	protected $adapter;

	public function __construct(Adapter\AbstractAdapter $adapter)
	{
		$this->setAdapter($adapter);
	}

	public function setAdapter(Adapter\AbstractAdapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		if($this->adapter instanceof Adapter\AbstractAdapter)
		{
			return $this->adapter;
		}

		throw new Exception\AdapterNotSetException();
	}

	public function getConfig(array $options)
	{
		$config = $this->getAdapter()->getConfig($options);

		return $config;
	}
}
?>