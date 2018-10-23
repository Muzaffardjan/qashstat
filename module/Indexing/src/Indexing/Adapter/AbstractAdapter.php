<?php  
namespace Indexing\Adapter;

use Indexing\ResultSet\AbstractResultSet;
use Indexing\Index\IndexInterface;
use Indexing\Index\AbstractIndex;

abstract class AbstractAdapter
{
	/**
	 * @var Indexing\ResultSet\AbstractResultSet
	 */
	protected $resultSet;
	/**
	 * Sets resultset object
	 *
	 * @param Indexing\ResultSet\AbstractResultSet $resultSetObject
	 */
	public function setResultSetObject(AbstractResultSet $resultSetObject)
	{
		$this->resultSet = $resultSetObject;
	}

	/**
	 * Gets resultset object
	 *
	 * @return Indexing\ResultSet\AbstractResultSet
	 */
	public function getResultSetObject()
	{
		return $this->resultSet;
	}

	/**
	 * Finds matches by query
	 *
	 * @param mixed $query
	 * @return Indexing\ResultSet\AbstractResultSet
	 */
	abstract function find($query);
	/**
	 * Adds new index
	 *
	 * @throws Exception\InvalidIndexSpecification
	 * @param Indexing\Index\IndexInterface $index
	 */
	abstract function add(IndexInterface $index);
	/**
	 * Deletes index
	 *
	 * @param Indexing\Index\IndexInterface $index
	 */
	abstract function delete($index);
	/**
	 * Deletes all index data
	 */
	abstract function flush();
}
?>