<?php  
namespace Menu\Adapter\Exception;

class TableProviderNotSetException extends \Exception
{
	public $message = 'DatabaseTable: Table provider not set!';
}
?>