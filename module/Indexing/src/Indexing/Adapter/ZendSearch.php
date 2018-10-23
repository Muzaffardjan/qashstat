<?php  
namespace Indexing\Adapter;

use ZendSearch\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use Indexing\Index\IndexInterface;
use Indexing\Adapter\ZendSearch\Exception\StoragePathNotExists;

class ZendSearch extends AbstractAdapter
{
	protected $storage;

	/**
	 * Construct 
	 *
	 * @param string $storage
	 */
	public function __construct($storage)
	{
		$this->setIndexStorage($storage);
	}

	/**
	 * Sets index storage path  
	 *
	 * @param string $storage
	 * @throws Indexing\Adapter\ZendSearch\Exception\StoragePathNotExists 
	 * 			if storage path invalid
	 * @return Indexing\Adapter\ZendSearch
	 */	
	public function setIndexStorage($storage)
	{
		if(!realpath(dirname($storage)))
		{
			throw new StoragePathNotExists;
		}

		$this->storage = $storage;

		return $this;
	}

	/**
	 * Gets index storage path  
	 *
	 * @return string
	 */	
	public function getIndexStorage()
	{
		return $this->storage;
	}

	/**
	 * Creates or opens index
	 * 
	 * @return ZendSearch\Lucene\Index
	 */
	public function getIndex()
	{
		if(file_exists($this->storage))
		{
			return \ZendSearch\Lucene\Lucene::open($this->storage);
		}

		mkdir($this->storage, 0755);

		return \ZendSearch\Lucene\Lucene::create($this->storage);
	}

	/**
	 * {@inheritDoc}
	 */
	public function flush()
	{
		if(file_exists($this->storage))
		{
			foreach(new \DirectoryIterator($this->storage) as $file)
			{
				if($file->isDot())
				{
					continue;
				}

				if(!$file->isDir())
				{
					unlink($file->getPathname());
				}
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($query)
	{
		$hits = $this->getIndex()->find($query);

		$resultSet = $this->getResultSetObject();

		$resultSet->initialize($hits);

		return $resultSet;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add(IndexInterface $index)
	{
		$document = new \ZendSearch\Lucene\Document();
		$spec = $index->getIndexSpecification();

		if(!isset($spec['fields']))
		{
			throw new Exception\InvalidIndexSpecification;
		}

		foreach($spec['fields'] as $field)
		{
			if(!$field instanceof Document)
			{
				if($field['name'] == 'id')
				{
					throw new \Exception('id c\'ant be used as a name');
				}

				switch(strtolower($field['type']))
				{
					case 'text': 
						$field = Document\Field::text($field['name'], $field['value'], (isset($field['encoding']) ? $field['encoding'] : 'utf-8'));
					break;
					case 'unindexed': 
						$field = Document\Field::unIndexed($field['name'], $field['value'], (isset($field['encoding']) ? $field['encoding'] : 'utf-8'));
					break;
					case 'unstored': 
						$field = Document\Field::unStored($field['name'], $field['value'], (isset($field['encoding']) ? $field['encoding'] : 'utf-8'));
					break;
					case 'binary': 
						$field = Document\Field::binary($field['name'], $field['value'], (isset($field['encoding']) ? $field['encoding'] : 'utf-8'));
					break;
					case 'keyword': 
						$field = Document\Field::keyword($field['name'], $field['value'], (isset($field['encoding']) ? $field['encoding'] : 'utf-8'));
					break;
					default:
						throw new Exception\InvalidField;
				}
			}

			$document->addField($field);
		}

		$this->getIndex()->addDocument($document);
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($index)
	{
		if(!is_int($index))
		{
			$index = $index->id;
		}

		$this->getIndex()->delete($index);
		$this->getIndex()->commit();
		$this->getIndex()->optimize();
	}
}
?>