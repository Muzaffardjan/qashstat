<?php  
namespace Media\Tables;

class Videos
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

	public function fetchAll($where = null, $sort = 'time DESC', $limit = null)
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

	public function getVideoById($id)
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

	public function add($video)
	{
		if(!is_array($video) && !($video instanceof \Media\ArrayObject\Video))
			throw new \Exception('Data must be array or Media\ArrayObject\Video');

		if(is_array($video))
		{
			$temp   = $this->getTablegateway()->getResultSetObject()->getArrayObjectPrototype();
			$video 	= $temp->setData($video);
		}

		$this->getTablegateway()->insert($video->getData());
	}

	public function edit($video, $where = array())
	{
		if(!is_array($video) && $video instanceof \Media\ArrayObject\Video)
		{
			$where 	= array('id' => $video->id);
			$video 	= $video->getData();

			if(isset($video['id']))
			{
				unset($video['id']);
			}
		}

		if(!is_array($video))
		{
			throw new \Exception('Data must be array or Pages\ArrayObject\Video');
		}

		$this->getTablegateway()->update($video, $where);
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