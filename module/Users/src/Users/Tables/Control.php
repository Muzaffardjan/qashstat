<?php  
namespace Users\Tables;

use Application\Db\TablegatewayObjectInterface;
use Zend\Db\Tablegateway\Tablegateway;

class Control implements TablegatewayObjectInterface, \Users\Authentication\DatabaseTableInterface
{
	protected $tablegateway;

	public function setTablegateway(Tablegateway $tablegateway)
	{
		$this->tablegateway = $tablegateway;
	}

	public function getTablegateway()
	{
		return $this->tablegateway;
	}

	public function getCount()
	{
		$adapter 	= $this->getTablegateway()->getAdapter();
		$sql 		= $this->getTablegateway()->getSql();
		$select 	= $sql->select();

		$select->columns(array('cnt' => new \Zend\Db\Sql\Expression('COUNT(`id`)')));
		return (int)$adapter->query($sql->buildSqlString($select), $adapter::QUERY_MODE_EXECUTE)->current()->cnt;
	}

	/**
	 * @param int|Zend\Db\Sql\Where|Clojure|string $where
	 */
	public function fetchAll($where = null, $sort = 'id DESC', $limit = null)
	{
		return $this->getTablegateway()->select(function($select)use($where,$sort,$limit)
			{
				if($where)
				{
					$select->where($where);
				}

				if($limit)
				{
					$select->limit($limit);
				}

				$select->order($sort);
			});
	}

	/**
	 * @param int|Zend\Db\Sql\Where|Clojure|string $where
	 */
	public function get($where)
	{
		if(is_int($where) || $where * 1 > 0)
		{
			$where = array('id' => $where);
		}

		return $this->getTablegateway()->select($where);
	}

	public function getUser($credential)
	{
		$result = $this->getTablegateway()->select(array('login' => $credential));

		if($result->count())
		{
			$identity = new \Users\Identity\User();
			$identity->setArrayObject($result->current());

			return $identity;
		}

		return null;
	}

	public function add($control)
	{
		if(!is_array($control) && $control instanceof \Users\ArrayObject\Control)
			$control = $control->getData();

		if(!is_array($control))
			throw new \Exception('Data must be array or Users\ArrayObject\Control');

		$this->getTablegateway()->insert($control);
	}

	public function edit($control, $where = array())
	{
		if(!is_array($control) && $control instanceof \Users\ArrayObject\Control)
		{
			$where 		= array('id' => $control->id);
			$control 	= $control->getData();

			if(isset($control['id']))
			{
				unset($control['id']);
			}
		}

		if(!is_array($control))
		{
			throw new \Exception('Data must be array or Users\ArrayObject\Control');
		}

		$this->getTablegateway()->update($control, $where);
	}

	/**
	 * @param int|Zend\Db\Sql\Where|Clojure|string $where
	 */
	public function delete($where)
	{
		if(is_int($where) || $where * 1 > 0)
		{
			$where = array('id' => $where);
		}

		$this->getTablegateway()->delete($where);
	}
}
?>