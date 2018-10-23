<?php  
namespace Indexing\ResultSet;

use Traversable;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use Indexing\ResultSet\Adapter\AbstractResultSetAdapter;

abstract class AbstractResultSet implements Iterator, ResultSetInterface
{
	protected $isTraversable = false;
	protected $count;
	protected $data;
	protected $result;
    protected $adapter;

    public function setAdapter(AbstractResultSetAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        if(!$this->adapter instanceof AbstractResultSetAdapter)
        {
            throw new Exception\AdapterNotSet;
        }

        return $this->adapter;
    }

	/**
	 * Intiliazeses resultset object
	 *
	 * @param array|Traversable $traversable
	 */
	public function initialize($traversable)
	{
		if(is_array($traversable))
		{
			$this->isTraversable = false;
		}
		elseif($traversable instanceof Traversable)
		{
			$this->isTraversable = true;
		}
		else
		{
			throw new Exception\InvalidArgument;
		}

		$this->data = $traversable;
	}

	/**
     * Iterator: move pointer to next item
     *
     * @return void
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * Iterator: retrieve current key
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Iterator: get current item
     *
     * @return 
     */
    public function current()
    {
    	$current = current($this->data);
    	$result = $this->getResult();
    	$array = array();

    	foreach($this->getAdapter()->asArray($current) as $key => $value)
    	{
    		$array[$key] = $value;
    	}

    	$result->setIndexData($array);

        return $result;
    }

    /**
     * Iterator: is pointer valid?
     *
     * @return bool
     */
    public function valid()
    {
        if(current($this->data))
        {
        	return true;
        }

        return false;
    }

    /**
     * Iterator: rewind
     *
     * @return void
     */
    public function rewind()
    {
        if(is_array($this->data))
        {
            reset($this->data);
        }

        if($this->data instanceof Traversable)
        {
            $this->data->rewind();
        }
    }

    /**
     * Countable: return count of rows
     *
     * @return int
     */
    public function count()
    {
        if ($this->count !== null) {
            return $this->count;
        }
        $this->count = count($this->data);
        return $this->count;
    }

    /**
     * Sets result object
     * 
     * @param ResultObjectInterface $object
     * @return ResultSet
     */
    public function setResult(ResultObjectInterface $object)
    {
    	$this->result = $object;
    }

    /**
     * Gets result object
     * 
     * @return ResultObjectInterface $object
     */
    public function getResult()
    {
    	if(!$this->result instanceof ResultObjectInterface)
    	{
    		throw new Exception\ResultObjectNotSet;
    	}

    	return $this->result;
    }
}
?>