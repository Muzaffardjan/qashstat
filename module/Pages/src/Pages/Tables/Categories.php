<?php  
namespace Pages\Tables;

use Pages\ArrayObject\Category;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class Categories
{
    /**
     * @var TableGateway
     */
	protected $tablegateway;

    /**
     * @var ServiceManager
     */
	protected $serviceManager;

    /**
     * Categories constructor.
     * @param TableGateway $tableGateway
     * @param ServiceManager $serviceManager
     */
    public function __construct(TableGateway $tableGateway, ServiceManager $serviceManager)
    {
        $this->setTablegateway($tableGateway);
        $this->setServiceLocator($serviceManager);
    }

	public function setServiceLocator(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;

		return $this;
	}

	public function getServiceLocator()
	{
		return $this->serviceManager;
	}

	public function setTablegateway(TableGateway $tableGateway)
	{
		$this->tablegateway = $tableGateway;

		return $this;
	}

	public function getTablegateway()
	{
		return $this->tablegateway;
	}

	public function fetchAll($where = null, $sort = 'id DESC', $limit = null)
	{
		return $this->getTablegateway()->select(
			function($select) use($where, $sort, $limit) {
				if ($where) {
					$select->where($where);
				}

				if ($limit) {
					$select->limit($limit);
				}

				$select->order($sort);
			}
		);
	}

	public function getCategoryById($id)
	{
		return $this->getTablegateway()->select(array('id' => $id))->current();
	}

	public function getWith($where)
	{
		if ($where instanceof Select) {
			return $this->getTablegateway()->selectWith($where);
		}

		return $this->getTablegateway()->select($where);
	}

	public function add($category)
	{
		if(
			!is_array($category) &&
			!($category instanceof Category)
		) {
			throw new \Exception(
				'Data must be array or Pages\ArrayObject\Category'
			);
		}

		if (is_array($category)) {
			$temp = $this->getTablegateway()
			->getResultSetObject()
			->getArrayObjectPrototype();

			$category = $temp->setData($category);
		}

		$conflicts = $this->getTablegateway()->select(['url' => $category->url]);

		if ($conflicts->count()) {
			$category->url .= '-' . substr(md5(date('d-m-Y-His')), 0, 5);
		}

		$this->getTablegateway()->insert($category->getData());
	}

	public function edit($category, $where = array())
	{
		if (
		    !is_array($category) &&
            $category instanceof Category
        ) {
			$category = $category->getData();

			if (isset($category['id'])) {
				unset($category['id']);
			}
		}

		if (! is_array($category)) {
			throw new \Exception('Data must be array or Pages\ArrayObject\Category');
		}

		$this->getTablegateway()->update($category, ['url' => $category['url']]);
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