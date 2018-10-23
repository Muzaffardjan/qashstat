<?php  
namespace Media\Tables;

class Galleries
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

	public function getGalleryById($id)
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

	public function add($gallery)
	{
		if(!is_array($gallery) && !($gallery instanceof \Media\ArrayObject\Gallery))
			throw new \Exception('Data must be array or Media\ArrayObject\Gallery');

		if(is_array($gallery))
		{
			$temp 		= $this->getTablegateway()->getResultSetObject()->getArrayObjectPrototype();
			$gallery 	= $temp->setData($gallery);
		}

		$conflicts = $this->getTablegateway()->select(array('url' => $gallery->url));

		if($conflicts->count())
		{
			$gallery->url .= '-'.substr(md5(date('d-m-Y-His')), 0, 5);
		}

		$this->getTablegateway()->insert($gallery->getData());
	}

	public function edit($gallery, $where = array())
	{
		if(!is_array($gallery) && $gallery instanceof \Media\ArrayObject\Gallery)
		{
			$where 		= array('id' => $gallery->id);
			$gallery 	= $gallery->getData();

			if(isset($gallery['id']))
			{
				unset($gallery['id']);
			}
		}

		if(!is_array($gallery))
		{
			throw new \Exception('Data must be array or Pages\ArrayObject\Category');
		}

		$this->getTablegateway()->update($gallery, $where);
	}

	/**
	 * @param int|Zend\Db\Sql\Where|Closure|string $where
	 */
	public function delete($where)
	{
		if(is_int($where) || (is_string($where) && $where * 1 > 0))
		{
			$where = array('id' => $where);
		}

		$this->getTablegateway()->delete($where);
	}
}
?>