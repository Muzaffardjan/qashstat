<?php  
namespace Menu\Tables;

use Menu\Adapter\DatabaseTable\TableProviderInterface;

class Menu implements TableProviderInterface
{
	protected $tablegateway;
	protected $serviceManager;

	public function __construct(\Zend\Db\TableGateway\TableGateway $tablegateway, \Zend\ServiceManager\ServiceManager $sm)
	{
		$this->setTablegateway($tablegateway);
		$this->setServiceLocator($sm);
	}

	public function setServiceLocator(\Zend\ServiceManager\ServiceManager $sm)
	{
		$this->serviceManager = $sm;

		return $this;
	}

	public function getServiceLocator()
	{
		return $this->serviceManager;
	}

	public function setTablegateway(\Zend\Db\TableGateway\TableGateway $tablegateway)
	{
		$this->tablegateway = $tablegateway;

		return $this;
	}

	public function getTablegateway()
	{
		return $this->tablegateway;
	}

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

	public function getMenuById($id)
	{
		return $this->getTablegateway()->select(array('id' => $id))->current();
	}

	public function getWith($where)
	{
		if($where instanceof \Zend\Db\Sql\Select)
		{
			return $this->getTablegateway()->selectWith($where);
		}

		return $this->getTablegateway()->select($where);
	}

	public function getMenu(array $options)
	{
		$menu = $this->getWith($options)->current();

		return $menu ? $menu->getConfig() : $menu;
	}

	public function add($menu)
	{
		if(!is_array($menu) && !($menu instanceof \Menu\ArrayObject\Menu))
			throw new \Exception('Data must be array or Menu\ArrayObject\Menu');

		if(is_array($menu))
		{
			$temp = $this->getTablegateway()->getResultSetObject()->getArrayObjectPrototype();
			$menu = $temp->setData($menu);
		}

		if($menu->position)
		{
			$this->getTablegateway()->update(array('position' => null), array('position' => $menu->position, 'locale' => $menu->locale));
		}

		$this->getTablegateway()->insert($menu->getData());
	}

	public function edit($menu, $where = array())
	{
		if(!is_array($menu) && $menu instanceof \Menu\ArrayObject\Menu)
		{
			$where 		= array('id' => $menu->id);
			$menu 	= $menu->getData();

			if(isset($menu['id']))
			{
				unset($menu['id']);
			}
		}

		if(!is_array($menu))
		{
			throw new \Exception('Data must be array or Menu\ArrayObject\Menu');
		}

		if(isset($menu['position']) && $menu['position'])
		{
			$this->getTablegateway()->update(array('position' => null), array('position' => $menu['position'], 'locale' => $menu['locale']));
		}

		$this->getTablegateway()->update($menu, $where);
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