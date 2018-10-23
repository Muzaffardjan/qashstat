<?php  
namespace Quiz\Questions;

interface AnswerInterface
{
	/**
	 * Gets order of answer
	 *
	 * @return int
	 */
	public function getOrder();

	/**
	 * Gets question id of answer
	 *
	 * @return int
	 */
	public function getQuestionId();
} 
?>