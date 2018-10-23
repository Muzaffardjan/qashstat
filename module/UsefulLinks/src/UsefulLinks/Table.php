<?php  
namespace UsefulLinks;

use UsefulLinks\Links\LinkInterface;
use UsefulLinks\Exception\BadMethodCallException;

class Table 
{
	/**
	 * @var Zend\Db\TableGateway\TableGateway
	 */
	protected $tableGateway;

	/**
	 * Construct
	 *
	 * @param Zend\Db\TableGateway\TableGateway $tableGateway
	 */
	public function __construct($tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Fetch all records from table
	 *
	 * @param mixed $order Order
	 * @return ResultSet
	 */
	public function fetchAll($order = 'id DESC')
	{
		$select = $this->tableGateway->getSql()->select();

		// Set order
		$select->order($order);

		return $this->tableGateway->selectWith($select);
	}

	/**
	 * Gets max order number in table
	 *
	 * @param string $locale
	 * @return int
	 */
	public function getMaxOrder($locale = null)
	{
		$select = $this->tableGateway->getSql()->select();

		$select->columns(array(
			'maxOrder' => new \Zend\Db\Sql\Expression('MAX(order_number)'),
		));

		if(null !== $locale)
		{
			$select->where(array('locale' => $locale));
		}

		if($result = $this->tableGateway->selectWith($select)->current())
		{
			return (int) $result->maxOrder;
		}

		return 0;
	}

	/**
	 * Gets link by it's id
	 *
	 * @param int $id
	 * @return false|LinkInterface
	 */
	public function getById($id)
	{
		return $this->tableGateway->select(array('id' => (int) $id))->current();
	}

	/**
	 * Gets links by locale
	 *
	 * @param string $locale
	 * @param mixed $order
	 * @return ResultSet
	 */
	public function getByLocale($locale, $order = 'id DESC')
	{
		$select = $this->tableGateway->getSql()->select();

		$select->where(array('locale' => $locale));
		$select->order($order);

		return $this->tableGateway->selectWith($select);
	}

	/**
	 * Sets order of given link by id to given order number
	 *
	 * @param int $oldOrder
	 * @param int $newOrder
	 */
	public function setOrderOf($oldOrder, $newOrder, $locale)
	{
		$this->tableGateway->update
		(
			array(
				'order_number' => (int) $newOrder,
			),
			array(
				'order_number' 	=> (int) $oldOrder,
				'locale'		=> $locale,
			)
		);
	}

	/**
	 * Adds new record to the table
	 *
	 * @param LinkInterface $link
	 */
	public function add(LinkInterface $link)
	{
		// Get hydrator
		$hydrator 	= $this->tableGateway->getResultSetPrototype()->getHydrator();
		// Extract data
		$data 		= $hydrator->extract($link);

		// Add new record
		$this->tableGateway->insert($data);
	}

	/**
	 * Updates existing record in the table
	 *
	 * @param LinkInterface $link
	 */
	public function update(LinkInterface $link)
	{
		// Get hydrator
		$hydrator 	= $this->tableGateway->getResultSetPrototype()->getHydrator();
		// Extract data
		$data 		= $hydrator->extract($link);
		// Where array
		$predicate	= array('id' => $data['id']);

		// We don't want to update id
		unset($data['id']);

		// Update record
		$this->tableGateway->update($data, $predicate);
	}

	/**
	 * Deletes existing record from table
	 *
	 * @throws BadMethodCallException 
	 *			if $where 
	 * @param mixed $where
	 */
	public function delete($where)
	{	
		if(!is_object($where) && (int) $where > 0)
		{
			$where 		= array('id' => (int) $where);
		}
		elseif($where instanceof LinkInterface)
		{
			// Get hydrator
			$hydrator 	= $this->tableGateway->getResultSetPrototype()->getHydrator();
			// Extract data
			$data 		= $hydrator->extract($link);
			$where 		= array('id' => $data['id']);
		}
		elseif(!is_callable($where) && !is_array($where))
		{
			throw new BadMethodCallException('Invalid \'$where\' provided');
		}

		$this->tableGateway->delete($where);
	}
}
?>