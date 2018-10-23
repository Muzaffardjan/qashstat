<?php  
namespace Feedback\Sender\Exception;

class ConfigNotFoundException extends \Exception 
{
	public $message = 'Sender config not found';
}
?>