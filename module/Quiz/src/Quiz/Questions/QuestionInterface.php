<?php  
/**
 * Quiz module for Foreach.Soft Mod355 v2
 *
 * Question interface
 * 
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://each.uz)
 * @author Kahramonov Javlonbek <kakjavlon@gmail.com>, 
 *		   Erkin Pardayev <erkin.pardayev@gmail.com>
 */ 
namespace Quiz\Questions;

use Quiz\Questions\Statistics\StatisticsProviderInterface;

interface QuestionInterface
{
	public function addAnswer(Answer $answer);
}
?>