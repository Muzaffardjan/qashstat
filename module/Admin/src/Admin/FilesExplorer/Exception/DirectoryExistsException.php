<?php  
namespace Admin\FilesExplorer\Exception;

class DirectoryExistsException extends \Exception
{
	protected $message = 'Directory with this name already exists';
}
?>