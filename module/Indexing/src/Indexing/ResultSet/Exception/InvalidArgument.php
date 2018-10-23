<?php  
namespace Indexing\ResultSet\Exception;

use Exception;

class InvalidArgument extends Exception
{
	public $message = 'Argument must be array or Traversable object';
}
?>