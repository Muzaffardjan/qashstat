<?php  
namespace Feedback\Sender\Exception;

class TransportNotFoundException extends \Exception 
{
	public $message = 'Transport class not found';
}
?>