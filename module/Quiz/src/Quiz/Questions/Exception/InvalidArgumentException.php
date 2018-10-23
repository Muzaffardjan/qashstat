<?php  
/**
 * Quiz module for Foreach.Soft Mod355 v2
 *
 * Invalid argument exception
 * 
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://each.uz)
 * @author Kahramonov Javlonbek <kakjavlon@gmail.com>, 
 *		   Erkin Pardayev <erkin.pardayev@gmail.com>
 */ 
namespace Quiz\Questions\Exception;

class InvalidArgumentException extends \Exception 
{
	public $message = 'Invalid argument provided';

	public function __construct($message = null)
	{
		if($message !== null)
		{
			$this->message = $message;
		}
	}
}
?>