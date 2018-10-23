<?php  
namespace Indexing;

use Indexing\Adapter\AbstractAdapter;
use Indexing\ResultSet\AbstractResultSet;
use Indexing\Index\IndexInterface;
use Indexing\Exception\AdapterNotSet;
use Indexing\Exception\ResultSetObjectNotSet;

class Indexing
{
	/**
	 * Adapter holder
	 *
	 * @var Indexing\Adapter\AbstractAdapter
	 */
	protected $adapter;

	/**
	 * Sets Adapter
	 *
	 * @param Indexing\Adapter\AbstractAdapter $adapter
	 * @return Indexing\Indexing;
	 */
	public function setAdapter(AbstractAdapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	/**
	 * Gets Adapter
	 *
	 * @throws Indexing\Exception\AdapterNotSet
	 * 			if adapter is not Indexing\Adapter\AbstractAdapter
	 * @return Indexing\Adapter\AbstractAdapter
	 */
	public function getAdapter()
	{
		if(!$this->adapter instanceof AbstractAdapter)
		{
			throw new AdapterNotSet;
		}

		return $this->adapter;
	}

	/**
	 * Sets resultset object
	 *
	 * @param Indexing\ResultSet\AbstractResultSet $resultSetObject
	 * @return Indexing\Indexing
	 */
	public function setResultSetObject(AbstractResultSet $resultSetObject)
	{
		$this->getAdapter()->setResultSetObject($resultSetObject);
		return $this;
	}

	/**
	 * Gets resultset object
	 *
	 * @throws Indexing\Exception\ResultSetObjectNotSet
	 *			if resultset object not set
	 * @return Indexing\ResultSet\AbstractResultSet
	 */
	public function getResultSetObject()
	{
		$result = $this->getAdapter()->getResultSetObject();

		if(!$result instanceof AbstractResultSet)
		{
			throw new Exception\ResultSetObjectNotSet;
		}

		return $result;
	}

	public function find($query)
	{
		return $this->getAdapter()->find($query);
	}

	public function add(IndexInterface $index)
	{
		return $this->getAdapter()->add($index);
	}

	public function delete($index)
	{
		return $this->getAdapter()->delete($index);
	}
}
?>