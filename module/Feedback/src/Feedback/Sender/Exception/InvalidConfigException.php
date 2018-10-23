<?php  
namespace Feedback\Sender\Exception;

class InvalidConfigException extends \Exception 
{
	public $message = 'Invalid sender config';
}
?>