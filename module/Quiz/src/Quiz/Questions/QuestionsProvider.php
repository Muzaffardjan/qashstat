<?php
namespace Quiz\Questions;

use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Adapter\Adapter;
use Quiz\Questions\Question;
use Quiz\Questions\Answer;
use Quiz\Questions\Tables\Questions;
use Quiz\Questions\Tables\Answers;
use Quiz\Questions\Tables\TablesInterface;

class QuestionsProvider implements QuestionsProviderInterface, AdapterAwareInterface 
{
	/**
	 * Questions table name 
	 *
	 * @const string
	 */
	const QUESTIONS_TABLE_NAME = 'quiz_questions';

	/**
	 * Answers table name 
	 *
	 * @const string
	 */
	const ANSWERS_TABLE_NAME = 'quiz_answers';

	/**
	 * @var Zend\Db\Adapter\Adapter
	 */
	protected $adapter;

	/**
	 * @var Quiz\Questions\Tables\TablesInterface
	 */
	protected $questionsTable;

	/**
	 * @var Quiz\Questions\Tables\TablesInterface
	 */
	protected $answersTable;

	/**
	 * {@inheritDoc}
	 */
	public function setDbAdapter(Adapter $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * Gets db adapter
	 *
	 * @return Zend\Db\Adapter\Adapter;
	 */
	public function getDbAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Sets questions table provider
	 *
	 * @param Quiz\Questions\Tables\TablesInterface $table
	 * @return QuestionsProvider
	 */
	public function setQuestionsTable(TablesInterface $table)
	{
		$this->questionsTable = $table;

		return $this;
	}

	/**
	 * Gets questions table provider
	 *
	 * @return Quiz\Questions\Tables\Questions
	 */
	public function getQuestionsTable()
	{
		if(null === $this->questionsTable)
		{
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->setArrayObjectPrototype(new Question());

			$questionsTable = new Questions();
			$questionsTable->setTableGateway
			(
				new \Zend\Db\TableGateway\TableGateway
				(
					self::QUESTIONS_TABLE_NAME, 
					$this->getDbAdapter(), 
					null, 
					$resultSet
				)
			);

			$this->setQuestionsTable($questionsTable);
		}

		return $this->questionsTable;
	}

	/**
	 * Sets answers table provider
	 *
	 * @param Quiz\Questions\Tables\TablesInterface $table
	 */
	public function setAnswersTable(TablesInterface $table)
	{
		$this->answersTable = $table;

		return $this;
	}

	/**
	 * Gets answers table provider
	 *
	 * @return Quiz\Questions\Tables\Answers
	 */
	public function getAnswersTable()
	{
		if(null === $this->answersTable)
		{
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->setArrayObjectPrototype(new Answer());

			$answersTable = new Answers();
			$answersTable->setTableGateway
			(
				new \Zend\Db\TableGateway\TableGateway
				(
					self::ANSWERS_TABLE_NAME, 
					$this->getDbAdapter(), 
					null, 
					$resultSet
				)
			);

			$this->setAnswersTable($answersTable);
		}

		return $this->answersTable;
	}

	/**
	 * Saves new question to table
	 *
	 * @param Question $question
	 * @return QuestionsProvider
	 */
	public function addQuestion(Question $question)
	{
		$this->getQuestionsTable()->addNew($question);

		return $this;
	}

	/**
	 * Saves new answer to table 
	 *
	 * @param Answer $answer
	 * @param Question $question the question which answer will be attached
	 * @throws Exception\InvalidArgumentException in variety cases
	 * @return QuestionsProvider
	 */
	public function addAnswer(Answer $answer, Question $question)
	{
		$questionsTable = $this->getQuestionsTable();
		$answersTable 	= $this->getAnswersTable();
 
		// check if question have unique id
		if(!(property_exists($question, $questionsTable->getColumns()['id']) && $question->{$questionsTable->getColumns()['id']}))
		{
			throw new Exception\InvalidArgumentException(
				'Invalid question given: Question must have unique id'
			);
		}

		// Set to answer question id
		$answer->{$answersTable->getColumns()['question']} = $question->{$questionsTable->getColumns()['id']};

		// Save
		$answersTable->addNew($answer);

		return $this;
	}

	/**
	 * Updates question data
	 *
	 * @param Question $question
	 * @return QuestionProvider
	 */
	public function updateQuestion(Question $question)
	{
		$this->getQuestionsTable()->update($question);

		return $this;
	}

	/**
	 * Updates answer data
	 *
	 * @param Answer $answer
	 * @return QuestionProvider
	 */
	public function updateAnswer(Answer $answer)
	{
		$this->getAnswersTable()->update($answer);

		return $this;
	}

	/**
	 * Deletes question
	 *
	 * @param Question $question
	 * @throws Exception\InvalidArgumentException in variety cases
	 * @return QuestionsProvider
	 */
	public function deleteQuestion(Question $question)
	{
		$questionsTable = $this->getQuestionsTable();
		$answersTable 	= $this->getAnswersTable();

		// check if question have unique id
		if(!(property_exists($question, $questionsTable->getColumns()['id']) && $question->{$questionsTable->getColumns()['id']}))
		{
			throw new Exception\InvalidArgumentException(
				'Invalid question given: Question must have unique id'
			);
		}

		// Delete answers first
		$answersTable->delete(array(
			$answersTable->getColumns()['question'] => $question->{$questionsTable->getColumns()['id']},
		));

		// Delete question itself
		$questionsTable->delete($question);

		return $this;
	}

	/**
	 * Deletes answer
	 * 
	 * @param Answer $answer
	 * @throws Exception\InvalidArgumentException if answer hasn't unique id
	 * @return QuestionsProvider
	 */
	public function deleteAnswer(Answer $answer)
	{
		$answersTable 	= $this->getAnswersTable();

		if(!(property_exists($answer, $answersTable->getColumns()['id']) && $answer->{$answersTable->getColumns()['id']}))
		{
			throw new Exception\InvalidArgumentException(
				'Invalid answer given: Answer must have unique id'
			);
		}

		$answersTable->delete($answer);

		return $this;
	}

	/**
	 * Gets question by id 
	 * 
	 * @param bool $mergeAnswers
	 * 			should merge answers default if true
	 * @return QuestionInterface|false
	 */
	public function getQuestion($id, $mergeAnswers = true)
	{
		$tableQuestions = $this->getQuestionsTable();

		// Get question
		$question = $tableQuestions->getById($id);

		// Get and merge answers if question exists and is required
		if($question && $mergeAnswers)
		{
			$tableAnswers 	= $this->getAnswersTable();

			// Get answers
			$answers = $tableAnswers->getByQuestionId($question->id);
			
			// If there are any answer set it to question
			if($answers->count())
			{
				$orderedAnswers = array();

				foreach($answers as $answer)
				{
					$orderedAnswers[$answer->getOrder()] = $answer;
				}

				$question->setAnswers($orderedAnswers);
			}

			return $question;
		}

		// if question not exists
		return false;
	}

	/**
	 * Gets Answer by id 
	 * 
	 * @param int $id
	 * @return QuestionInterface|false
	 */
	public function getAnswer($id)
	{
		$answersTable = $this->getAnswersTable();

		// Get answer
		$answer = $answersTable->getBy(array(
			$answersTable->getColumns()['id'] => $id,
		))->current();

		// Return if answer exists
		if($answer)
		{
			return $answer;
		}

		// if question not exists
		return false;
	}

	/**
	 * Gets random question
	 * 
	 * @param bool $mergeAnswers
	 * 			should merge answers default if true
	 * @return QuestionInterface|false
	 */
	public function getRandomQuestion($locale = null, $mergeAnswers = true)
	{
		$tableQuestions = $this->getQuestionsTable();

		// Get question
		$question = $tableQuestions->getRandom($locale);

		// Get and merge answers if question exists and is required
		if($question && $mergeAnswers)
		{
			$tableAnswers 	= $this->getAnswersTable();

			// Get answers
			$answers = $tableAnswers->getByQuestionId($question->id);
			
			// If there are any answer set it to question
			if($answers->count())
			{
				$orderedAnswers = array();

				foreach($answers as $answer)
				{
					$orderedAnswers[$answer->getOrder()] = $answer;
				}

				$question->setAnswers($orderedAnswers);
			}

			return $question;
		}

		// if question not exists
		return false;
	}

	/**
	 * Gets all questions
	 *
	 * @param string $locale optional Locale of the questions if needed
	 * @param bool $mergeAnswers Should merge answers
	 * @param int|string $limit 
	 */
	public function getQuestions($locale = null, $mergeAnswers = true, $limit = null)
	{
		$tableQuestions = $this->getQuestionsTable();

		if(null === $locale)
		{
			$resultset = $tableQuestions->getAll();
		}
		else
		{
			$resultset = $tableQuestions->getByLocale($locale);
		}

		$questions 		= array();
		$questions_id 	= array();

		// Set resulted questions to array
		// keys represent question id for further use
		foreach($resultset as $question)
		{
			$currentId 				= $question->{$tableQuestions->getColumns()['id']};
			$questions_id[] 		= $currentId;
			$questions[$currentId] 	= $question;
		}

		if($mergeAnswers)
		{
			$tableAnswers 	= $this->getAnswersTable();
			$answers 		= array();

			// Get answers
			if($questions_id)
			{
				$answers = $tableAnswers->getBy(array(
					$tableAnswers->getColumns()['question'] => $questions_id,
				));
			}

			$orderedAnswers = array();
			
			// If there are any answer set it to questions
			foreach($answers as $answer)
			{
				$currentQuestion = $answer->{$tableAnswers->getColumns()['question']};
				$orderedAnswers[$currentQuestion][$answer->getOrder()] = $answer;
			}

			foreach($orderedAnswers as $key => $value)
			{
				if(isset($questions[$key]))
				{
					$questions[$key]->setAnswers($value);
				}
			}
		}

		return $questions;
	}

	/**
	 * Gets questions by vote count will be sorted by count biggest first
	 *
	 * @param string $locale
	 * @param bool $mergeAnswers Should merge answers default is 'true'
	 * @param string|int $limit
	 * @return array
	 */
	public function getByVoteCount($locale = null, $mergeAnswers = true, $limit = null)
	{
		$questions_id = array();

		foreach($this->getAnswersTable()->getByVoteCount($limit) as $item)
		{
			$questions_id[] = $item->question;
		}

		$resultset = array();

		// Get questions
		if($questions_id)
		{	
			$resultset = $this->getQuestionsTable()
			->getBy(array(
				$this->getQuestionsTable()->getColumns()['id'] => $questions_id,
			));
		}
			
		$questions = array();

		// Set resulted questions to array
		// keys represent question id for further use
		foreach($resultset as $question)
		{
			$questions[$question->{$this->getQuestionsTable()->getColumns()['id']}] = $question;
		}

		if($mergeAnswers)
		{
			$tableAnswers 	= $this->getAnswersTable();
			$answers 		= array();

			// Get answers
			if($questions_id)
			{
				$answers = $tableAnswers->getBy(array(
					$tableAnswers->getColumns()['question'] => $questions_id,
				));
			}
			
			$orderedAnswers = array();
			
			// If there are any answer set it to questions
			foreach($answers as $answer)
			{
				$currentQuestion = $answer->{$tableAnswers->getColumns()['question']};
				$orderedAnswers[$currentQuestion][$answer->getOrder()] = $answer;
			}

			foreach($orderedAnswers as $key => $value)
			{
				if(isset($questions[$key]))
				{
					$questions[$key]->setAnswers($value);
				}
			}
		}

		return $questions;
	}
}