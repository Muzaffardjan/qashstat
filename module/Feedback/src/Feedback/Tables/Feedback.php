<?php
namespace Feedback\Tables;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Feedback
{
	protected $tableGateway;

	public function __construct(\Zend\Db\TableGateway\TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function getCount()
	{
		$result = $this->tableGateway->select(array('checked' => 0));
		$count 	= $result->count();
		return $count; 
	}

	public function getNewFeedback()
	{
		$result = $this->tableGateway->select(function(Select $select) {
				$select->where(array('checked' => 0));
				$select->order('id DESC');
			});
		return $result;
	}

	public function saveFeedback(\Feedback\ArrayObject\Feedback $content)
	{
		$data = array(
				'name'		=> $content->name,
				'phone'		=> $content->phone,
				'email' 	=> $content->email,
				'subject'	=> $content->subject,
				'text' 		=> $content->text,
				'time'		=> $content->time,
				'checked'	=> $content->checked,
			);
		
		$this->tableGateway->insert($data);
	}

	public function fetchAll()
	{
		$feedbacks = $this->tableGateway->select(function(Select $select) {
				$select->order('id DESC');
			});
		return $feedbacks;
	}

	public function getFeedback($id)
	{
		$id = (int)$id;
		$feedback = $this->tableGateway->select(array('id' => $id));
		$row = $feedback->current();
		if ($row)
		{
			return $row;
		}
		else
		{
			throw new \Exception('Could not find feedback with $id');
		}
	}

	public function updateFeedbackStatus($id)
	{
		$data = array('checked' => 1);
		$status = $this->tableGateway->update($data, array('id' => $id));
	}

	public function delete($id)
	{
		$res = $this->tableGateway->delete(array('id' => $id));
		return $res;
	}
}

?>