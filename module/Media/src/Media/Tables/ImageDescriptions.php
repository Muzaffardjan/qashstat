<?php  
namespace Media\Tables;

class ImageDescriptions
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

	public function fetchAll($where = null, $limit = null)
	{
		return $this->getTablegateway()->select(function($select) use ($where, $limit)
			{
				if($where)
				{
					$select->where($where);
				}

				if($limit)
				{
					$select->limit($limit);
				}
			});
	}

	public function getByImageId($videoId)
	{
		return $this->getTablegateway()->select(array('image_id' => $videoId));
	}

	public function getDescriptionById($id)
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

	public function add($description)
	{
		if(!is_array($description) && !($description instanceof \Media\ArrayObject\ImageDescription))
			throw new \Exception('Data must be array or Media\ArrayObject\ImageDescription');

		if(is_array($description))
		{
			$temp        = $this->getTablegateway()->getResultSetObject()->getArrayObjectPrototype();
			$description = $temp->setData($description);
		}

		$this->getTablegateway()->insert($description->getData());
	}

	public function edit($description, $where = array())
	{
		if(!is_array($description) && $description instanceof \Media\ArrayObject\ImageDescription)
		{
			$where 	     = array('id' => $description->id);
			$description = $description->getData();

			if(isset($description['id']))
			{
				unset($description['id']);
			}
		}

		if(!is_array($description))
		{
			throw new \Exception('Data must be array or Pages\ArrayObject\ImageDescription');
		}

		$this->getTablegateway()->update($description, $where);
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