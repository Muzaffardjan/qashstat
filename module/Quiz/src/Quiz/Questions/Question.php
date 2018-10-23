<?php  
namespace Quiz\Questions;
 
class Question extends AbstractQuestion
{
	/**
	 *	@var int|string $id Unique id of question
	 */
	public $id;

	/**
	 *	@var string $text Holds question
	 */
	public $text;

	/**
	 *	@var string $locale Holds locale of the question
	 */
	public $locale;
}
?>