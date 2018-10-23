<?php  
/**
 * Quiz module for Foreach.Soft Mod355 v2
 *
 * Answers not set exception
 * 
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://each.uz)
 * @author Kahramonov Javlonbek <kakjavlon@gmail.com>, 
 *		   Erkin Pardayev <erkin.pardayev@gmail.com>
 */ 
namespace Quiz\Questions\Exception;

class AnswersNotSetException extends \Exception 
{
	public $message = 'Answers must be set first';

	public function __construct($message = null)
	{
		if($message !== null)
		{
			$this->message = $message;
		}
	}
}
?>