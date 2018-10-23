<?php  
/**
 * Quiz module for Foreach.Soft Mod355 v2
 * 
 * Questions database table driver
 *
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://each.uz)
 * @author Kahramonov Javlonbek <kakjavlon@gmail.com>, 
 *		   Erkin Pardayev <erkin.pardayev@gmail.com>
 */
namespace Quiz\Questions\Tables;

use Zend\Db\TableGateway\TableGatewayInterface;
use Quiz\Questions\Question;

class Questions implements TablesInterface
{
	/**
	 * @var array $columns Holds name of columns of the table
	 */
	protected $columns = array(
		'id' 		=> 'id',
		'text' 		=> 'text',
		'locale' 	=> 'locale',
	);

	/**
	 * @var TableGatewayInterface Holds table gateway of answers table
	 */
	protected $tableGateway;

	/**
	 * {@inheritDoc}
	 */
	public function setTableGateway(TableGatewayInterface $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTableGateway()
	{
		if(null === $this->tableGateway)
		{
			throw new Exception\TableGatewayNotSetException;
		}

		return $this->tableGateway;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setColumns($columns)
	{
		if(is_array($columns))
		{
			$this->columns = $columns;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Gets all questions
	 *
	 * @return Zend\Db\ResultSet\ResultSet
	 */
	public function getAll($order = 'id DESC')
	{
		$select = $this->getTableGateway()->getSql()->select();

		$select
		->order($order);

		return $this->getTableGateway()->selectWith($select);
	}

	/**
	 * Gets questions by condition
	 *
	 * @param array|Callable $where Condition
	 * @return Zend\Db\ResultSet\ResultSet
	 */
	public function getBy($where)
	{
		return $this->getTableGateway()->select($where);
	}

	/**
	 * Gets question by id
	 *
	 * @return false|Question Returns question if exists else false returned
	 */
	public function getById($id)
	{
		return $this->getTableGateway()->select(array('id' => $id))->current();
	}

	/**
	 * Gets random record
	 *
	 * @param string $locale
	 * @return QuestionInterface|false
	 */
	public function getRandom($locale = null)
	{
		$select = $this->getTableGateway()->getSql()->select();

		if(null !== $locale)
		{
			$select->where(array(
				'locale' => $locale,
			));
		}

		$select
		->order(new \Zend\Db\Sql\Expression('RAND()'))
		->limit(1);

		return $this->getTableGateway()->selectWith($select)->current();
	}

	/**
	 * Gets questions by locale
	 *
	 * @param string $locale
	 * @param string|int $limit Default is null
	 * @param string $order Default is 'id DESC'
	 * @return Zend\Db\ResultSet\ResultSet
	 */
	public function getByLocale($locale, $limit = null, $order = 'id DESC')
	{
		$select = $this->getTableGateway()->getSql()->select();

		$select->where(array('locale' => $locale))
		->order($order);

		if(null !== $limit)
		{
			$select->limit($limit);
		}

		return $this->getTableGateway()->selectWith($select);
	}

	/**
	 * Inserts new question to table
	 *
	 * @throws Exception\InvalidArgumentException if the provided argument is not of type
	 * 		'array'.
	 *		'Question'.
	 * @param array|Question $question Question that needs to be added
	 */
	public function addNew($question)
	{
		if($question instanceof Question)
		{
			$question = $question->getData();
		}
		else if(!is_array($question))
		{
			throw new Exception\InvalidArgumentException(
				sprintf(
					'Argument must be array or \'%s\' \'%s\' given', 
					'Quiz\Questions\Question', 
					gettype($question) == 'object'? get_class($question): gettype($question)
				)
			);
		}

		// Insert to table
		$this->getTableGateway()->insert($question);
	}

	/**
	 * Updates existing question in table
	 *
	 * @throws Exception\InvalidArgumentException if the provided argument is not of type
	 * 		'array'.
	 *		'Question'.
	 * @throws Exception\InvalidQuestionIdException if the provided question doesn't have id
	 * @param array|Question $question Question that needs to be added
	 */
	public function update($question)
	{
		if($question instanceof Question)
		{
			$question = $question->getData();
		}
		elseif(!is_array($question))
		{
			throw new Exception\InvalidArgumentException(
				sprintf(
					'Argument must be array or \'%s\' \'%s\' given', 
					'Quiz\Questions\Question', 
					gettype($question) == 'object'? get_class($question): gettype($question)
				)
			);
		}

		if(!isset($question[$this->columns['id']]))
		{
			throw new Exception\InvalidQuestionIdException('Invalid question id provided or not provided');
		}
		else
		{
			$id = array(
				$this->columns['id'] => $question[$this->columns['id']],
			);

			// No need to update unique id
			unset($question[$this->columns['id']]);
		}

		// Update question
		$this->getTableGateway()->update($question, $id);
	}

	/**
	 * Deletes question from table
	 * 
	 * @param int|Question|Where|Callable $where
	 */
	public function delete($where)
	{
		if(gettype($where) != 'object' && (int) $where > 0)
		{
			$where = array(
				$this->columns['id'] => (int) $where,
			);
		}
		elseif($where instanceof Question)
		{
			$where = array(
				$this->columns['id'] => (int) $where->{$this->columns['id']},
			);
		}
		
		// Delete question
		$this->getTableGateway()->delete($where);
	}
}
?>