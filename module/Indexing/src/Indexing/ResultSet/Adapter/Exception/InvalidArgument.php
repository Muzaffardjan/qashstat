<?php  
namespace Indexing\ResultSet\Adapter\Exception;

class InvalidArgument extends \Exception
{
	public $message = 'Invalid argument';

	public function __construct($message)
	{
		$this->message = $message;
	}
}
?>