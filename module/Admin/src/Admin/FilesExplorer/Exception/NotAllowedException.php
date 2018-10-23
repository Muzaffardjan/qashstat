<?php  
namespace Admin\FilesExplorer\Exception;

class NotAllowedException extends \Exception
{
	protected $message = 'You are not allowed to do this action';
}
?>