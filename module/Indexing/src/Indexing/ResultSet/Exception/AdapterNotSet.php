<?php  
namespace Indexing\ResultSet\Exception;

class AdapterNotSet extends \Exception
{
	public $message = 'Adapter not set or not instance of Indexing\ResultSet\Adapter\AbstractResultSetAdapter';
}
?>