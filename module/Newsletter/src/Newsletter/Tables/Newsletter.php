<?php
namespace Newsletter\Tables;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Newsletter
{
	protected $tableGateway;

	public function __construct(\Zend\Db\TableGateway\TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function subscribe(\Newsletter\ArrayObject\Newsletter $data)
	{
		$data = array(
				'email'	=> $data->email,
				'link'	=> $this->string(),
			);
		$this->tableGateway->insert($data);
	}

	public function unSubscribe($link)
	{
		$result = $this->tableGateway->delete(array('link' => $link));
	}

	public function fetchAll()
	{
		$subscribers = $this->tableGateway->select(function(Select $select) {
				$select->order('id DESC');
			});
		return $subscribers;
	}

	public function selectSubscriber()
	{
		$subscriber = $this->tableGateway->select();
		return $subscriber;
	}

	public function checkEmail($email)
	{
		$result = $this->tableGateway->select(array('email' => $email));
		$result->current();
		if ($result > 0)
		{
			return true;
		}
		return false;
	}

	public function string() {
	    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

	      // 32 bits for "time_low"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

	      // 16 bits for "time_mid"
	      mt_rand(0, 0xffff),

	      // 16 bits for "time_hi_and_version",
	      // four most significant bits holds version number 4
	      mt_rand(0, 0x0fff) | 0x4000,

	      // 16 bits, 8 bits for "clk_seq_hi_res",
	      // 8 bits for "clk_seq_low",
	      // two most significant bits holds zero and one for variant DCE1.1
	      mt_rand(0, 0x3fff) | 0x8000,

	      // 48 bits for "node"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	    );
  }
}

?>