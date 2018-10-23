<?php  
namespace Blocks\Exception;

class StorageNotSetException extends \Exception
{
	public $message = 'Storage not set or not instace of Blocks\Storage\Storage';
}
?>