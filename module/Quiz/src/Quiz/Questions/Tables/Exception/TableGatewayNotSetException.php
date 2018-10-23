<?php  
/**
 * Quiz module for Foreach.Soft Mod355 v2
 * 
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://each.uz)
 * @author Kahramonov Javlonbek <kakjavlon@gmail.com>, 
 *		   Erkin Pardayev <erkin.pardayev@gmail.com>
 */ 
namespace Quiz\Questions\Tables\Exception;

class TableGatewayNotSetException extends Exception
{
	public $message = 'TableGateway object must be set';
}
?>