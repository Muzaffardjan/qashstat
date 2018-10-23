<?php  
namespace Indexing\Index;

use Indexing\ResultSet\ResultObjectInterface;

class Index extends AbstractIndex
{
	protected $data;

	public function __get($key)
	{
		if(isset($this->data[$key]))
		{
			return $this->data[$key];
		}

		return null;
	}

	public function __set($key, $value)
	{
		if(isset($this->data[$key]))
		{
			$this->data[$key] = $value;
		}
	}

	public function setIndexData(array $data)
	{
		$this->data = $data;
	}

	public function getIndexSpecification()
	{
		$fields = array();

		foreach($this->data as $key => $value)
		{
			if(is_string($value) || is_numeric($value))
			{
				$fields[] = array(
					'type' 	=> 'Text',
					'name'	=> $key,
					'value'	=> $value,
				);
			}
		}

		return array(
			'fields' => $fields,
		);
	}

	public function toArray()
	{
		return $this->data;
	}
}
?>