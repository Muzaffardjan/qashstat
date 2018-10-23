<?php  
namespace Blocks\Storage\Adapter;

use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Sql;
use Blocks\Block;

class DatabaseTable extends AbstractAdapter
{
	protected $adapter;

	protected $sql;

	protected $tablename;

	protected $fields = array(
		'locale' 	=> 'locale',
		'name'		=> 'name',
		'content'	=> 'content',
	);

	public function __construct(DbAdapter $adapter, $tablename, $fields = null)
	{
		$this->adapter 		= $adapter;
		$this->sql 			= new Sql($adapter);
		$this->tablename 	= $tablename;

		if($fields)
		{
			foreach($this->fields as $key => $value)
			{
				if(isset($fields[$key]))
				{
					$this->fields[$key] = $value;
				}
			}
		}
	}

	public function fetchBlocks()
	{
		$select 	= $this->sql->select();
		$adapter 	= $this->adapter;
		$result 	= $adapter->query($this->sql->buildSqlString($select->from($this->tablename)), $adapter::QUERY_MODE_EXECUTE);
		$blocks 	= array();

		foreach($result as $item)
		{
			$block = new Block();

			foreach($this->fields as $key => $value)
			{
				if(property_exists($block, $key))
				{
					$block->{$key} = $item->{$value};
				}
			}

			$blocks[] = $block;
		}

		return $blocks;
	}

	public function saveBlock($name, $locale, $content)
	{
		$data = array(
			$this->fields['name'] 		=> $name,
			$this->fields['locale'] 	=> $locale,
			$this->fields['content'] 	=> $content,
		);

		$select 	= $this->sql->select();
		$adapter 	= $this->adapter;

		$select
		->from($this->tablename)
		->where(array(
			$this->fields['name'] 	=> $name,
			$this->fields['locale'] => $locale,
		));

		$result 	= $adapter->query($this->sql->buildSqlString($select), $adapter::QUERY_MODE_EXECUTE);
		
		if($result->count())
		{
			$save = $this->sql->update();
			$predicate = array(
				$this->fields['name'] 	=> $name,
				$this->fields['locale'] => $locale,
			);

			$save
			->table($this->tablename)
			->set(array($this->fields['content'] => $content))
			->where($predicate);
		}
		else
		{
			$save = $this->sql->insert();

			$save
			->into($this->tablename)
			->values($data);
		}

		$adapter->query($this->sql->buildSqlString($save), $adapter::QUERY_MODE_EXECUTE);
	}
}
?>