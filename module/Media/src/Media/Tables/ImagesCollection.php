<?php  
namespace Media\Tables;

class ImagesCollection
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

	public function getCollections()
	{
		return $this->getTablegateway()->select(function($select){
			$select->group('name');
		});
	}

	public function getByName($name)
	{
		return $this->getTablegateway()->select(array('name' => $name));
	}

	public function getImageById($id)
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

	public function add($image)
	{
		if(!is_array($image) && !($image instanceof \Media\ArrayObject\Image))
			throw new \Exception('Data must be array or Media\ArrayObject\Image');

		if(is_array($image))
		{
			$temp 	= $this->getTablegateway()->getResultSetObject()->getArrayObjectPrototype();
			$image 	= $temp->setData($image);
		}

		$this->getTablegateway()->insert($image->getData());
	}

	public function edit($image, $where = array())
	{
		if(!is_array($image) && $image instanceof \Media\ArrayObject\Image)
		{
			$where 		= array('id' => $image->id);
			$image 	= $image->getData();

			if(isset($image['id']))
			{
				unset($image['id']);
			}
		}

		if(!is_array($image))
		{
			throw new \Exception('Data must be array or Pages\ArrayObject\Category');
		}

		$this->getTablegateway()->update($image, $where);
	}

	/**
	 * @param int|Zend\Db\Sql\Where|Clojure|string $where
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