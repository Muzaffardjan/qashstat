<?php  
namespace Pages\Tables;

use Pages\AlternativesChain\Adapter\DatabaseTable\TableProvider;

class AlternativesChain implements TableProvider
{
	public function __construct(\Zend\Db\TableGateway\TableGateway $tablegateway)
	{
		$this->setTablegateway($tablegateway);
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

	/**
	 * {@inheritDoc}
	 */
	public function getChain($chainId)
	{
		return $this->getTablegateway()->select(array('chain' => $chainId));
	}

	/**
	 * {@inheritDoc}
	 */
	public function saveChain($chainId, array $items)
	{
		$tablegateway 	= $this->getTablegateway();
		$adapter 		= $tablegateway->getAdapter();
		$sql 			= $tablegateway->getSql();
		$inserts 		= array();

		foreach($items as $item)
		{
			$insert = $sql->insert();

			$insert
			->columns(array('chain', 'locale', 'id'))
			->values(array(
				'chain' 	=> $item->getChainId(),
				'locale' 	=> $item->getItemLocale(),
				'id' 		=> $item->getItemId(),
			));

			$inserts[] = $sql->buildSqlString($insert);
		}

		$adapter->query(join($inserts, ';'), $adapter::QUERY_MODE_EXECUTE);
	}
	/**
	 * {@inheritDoc}
	 */
	public function deleteItemsFromChains(array $arrayItemsId)
	{
		$this->getTablegateway()->delete(array('id' => $arrayItemsId));
	}
}
?>