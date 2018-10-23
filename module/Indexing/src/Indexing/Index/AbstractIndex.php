<?php  
namespace Indexing\Index;

use Indexing\ResultSet\ResultObjectInterface;

abstract class AbstractIndex implements ResultObjectInterface, IndexInterface
{
	/**
	 * @var array|Traversable
	 */
	protected $data;

	/**
	 * Returned result will be passed to adapter
	 *
	 * @return mixed
	 */
	public function getIndexSpecification()
	{
		/**
		 * @todo in childs
		 */
	}

	/**
	 * Sets result data
	 *
	 * @param arrat $data
	 */
	public function setIndexData(array $data)
	{
		$this->data = $data;
	}
}
?>