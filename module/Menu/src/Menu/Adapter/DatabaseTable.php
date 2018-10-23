<?php  
namespace Menu\Adapter;

class DatabaseTable extends AbstractAdapter
{
	protected $table;

	public function __construct(DatabaseTable\TableProviderInterface $table)
	{
		$this->setTableProvider($table);
	}

	public function setTableProvider(DatabaseTable\TableProviderInterface $table)
	{
		$this->table = $table;

		return $this;
	}

	public function getTableProvider()
	{
		if($this->table instanceof DatabaseTable\TableProviderInterface)
		{
			return $this->table;
		}

		throw new Exception\TableProviderNotSetException();
	}

	public function getConfig(array $options)
	{
		return $this->getTableProvider()->getMenu($options);
	}
}
?>