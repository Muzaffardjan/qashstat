<?php  
namespace Quiz\Questions\Tables;

use Zend\Db\TableGateway\TableGatewayInterface;
use Quiz\Questions\Answer;

class Answers implements TablesInterface
{
	/**
	 * @var array $columns Holds name of columns of the table
	 */
	protected $columns = array(
		'id' 		=> 'id',
		'question' 	=> 'question',
		'text' 		=> 'text',
		'voted'	 	=> 'voted',
		'index'	 	=> 'index',
	);

	/**
	 * @var TableGatewayInterface
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
	 * Gets answers by condition
	 *
	 * @param array|Callable $where Condition
	 * @return Zend\Db\ResultSet\ResultSet
	 */
	public function getBy($where)
	{
		return $this->getTableGateway()->select($where);
	}

	/**
	 * Gets answers by question id
	 *
	 * @return Zend\Db\ResultSet\ResultSet
	 */
	public function getByQuestionId($question)
	{
		return $this->getTableGateway()->select(array($this->columns['question'] => $question));
	}

	/**
	 * Gets question ids by summary vote count of answers
	 *
	 * @param string|int $limit Limit result
	 * @return Zend\Db\ResultSet\ResultSet
	 */
	public function getByVoteCount($limit = null)
	{
		$select 	= $this->getTableGateway()->getSql()->select();
		$adapter 	= $this->getTableGateway()->getAdapter();

		$select->columns(array(
			$this->columns['question'],
			$this->columns['voted'] => new \Zend\Db\Sql\Expression('SUM('.$this->columns['voted'].')'),
		))
		->order($this->columns['voted'] . ' DESC')
		->group($this->columns['question']);

		if(null !== $limit)
		{
			$select->limit($limit);
		}

		return $adapter->query($this->getTableGateway()->getSql()->buildSqlString($select), $adapter::QUERY_MODE_EXECUTE);
	}

	/**
	 * Changes order of Answer
	 *
	 * @param int $old Old order
	 * @param int $new Order to be set to
	 * @param int $question Id of question
	 * @return Answers
	 */
	public function setOrder($old, $new, $question)
	{
		$this->getTableGateway()->update
		(
			array(
				$this->columns['index'] => $new
			),
			array(
				$this->columns['index'] 	=> $old,
				$this->columns['question'] 	=> $question,
			)
		);

		return $this;
	}

	/**
	 * Inserts new answer to table
	 *
	 * @throws Exception\InvalidArgumentException if the provided argument is not of type
	 * 		'array'.
	 *		'Answer'.
	 * @param array|Answer $answer Answer that needs to be added
	 */
	public function addNew($answer)
	{
		if($answer instanceof Answer)
		{
			$answer = $answer->getData();
		}
		else if(!is_array($answer))
		{
			throw new Exception\InvalidArgumentException(
				sprintf(
					'Argument must be array or \'%s\' \'%s\' given', 
					'Quiz\Questions\Answer', 
					gettype($answer) == 'object'? get_class($answer): gettype($answer)
				)
			);
		}

		// Insert to table
		$this->getTableGateway()->insert($answer);
	}

	/**
	* Updates existing answer in table
	* 
	* @throws Exception\InvalidArgumentException if the provided argument is not of type
	* 		'array'.
	*		'Answer'.
	* @throws Exception\InvalidAnswerIdException if the provided question doesn't have id
	* @param array|Answer $question Answer that needs to be added
	*/

	public function update($answer)
	{
		if($answer instanceof Answer)
		{
			$answer = $answer->getData();
		}
		elseif(!is_array($answer))
		{
			throw new Exception\InvalidArgumentException(
				sprintf(
					'Argument must be array or \'%s\' \'%s\' given', 
					'Quiz\Answers\Answer', 
					gettype($answer) == 'object'? get_class($answer): gettype($answer)
				)
			);
		}

		if(!isset($answer[$this->columns['id']]))
		{
			throw new Exception\InvalidAnswerIdException('Invalid answer id provided or not provided');
		}
		else
		{
			$id = array(
				$this->columns['id'] => $answer[$this->columns['id']],
			);

			// No need to update unique id
			unset($answer[$this->columns['id']]);
		}

		// Update answer
		$this->getTableGateway()->update($answer, $id);
	}

	/**
	 * Deletes answer from table
	 * 
	 * @param int|Answer|Where|Callable $where
	 */
	public function delete($where)
	{	
		if(gettype($where) == 'string' && (int) $where > 0)
		{
			$where = array(
				$this->columns['id'] => (int) $where,
			);
		}
		elseif($where instanceof Answer)
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