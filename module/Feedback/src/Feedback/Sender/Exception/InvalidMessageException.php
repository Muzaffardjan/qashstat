<?php  
namespace Feedback\Sender\Exception;

class InvalidMessageException extends \Exception 
{
	public $message = 'Invalid message provided';
}
?>