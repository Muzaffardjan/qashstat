<?php  
namespace Quiz\Questions\Tables;

use Zend\Db\TableGateway\TableGatewayInterface;

interface TablesInterface
{
	/**
	 * Sets table gateway
	 * 
	 * @param TableGatewayInterface $tableGateway
	 */
	public function setTableGateway(TableGatewayInterface $tableGateway);
	
	/**
	 * Gets table gateway
	 *
	 * @return TableGatewayInterface
	 */
	public function getTableGateway();

	/**
	 * Sets column name keys
	 *
	 * @param $columns Array of column names
	 */
	public function setColumns($columns);

	/**
	 * Gets columns 
	 *
	 * @return array Columns key value array
	 */
	public function getColumns();
}
?>