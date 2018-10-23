<?php  
namespace Indexing\Adapter\Exception;

use Exception;

class InvalidIndexSpecification extends Exception
{
	public $message = 'Provided specfication is invalid';
}
?>