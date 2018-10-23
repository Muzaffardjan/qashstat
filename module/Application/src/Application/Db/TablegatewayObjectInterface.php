<?php  
namespace Application\Db;

use Zend\Db\Tablegateway\Tablegateway;

interface TablegatewayObjectInterface
{
	public function setTablegateway(Tablegateway $tablegateway);
	public function getTablegateway();
}
?>