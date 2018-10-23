<?php  
namespace Blocks\Storage;

use Blocks\Block;

class Storage 
{
	protected $adapter;
	protected $buffer;

	public function __construct(Adapter\AbstractAdapter $adapter)
	{
		$this->setAdapter($adapter);
		$this->buffer();
	}

	public function setAdapter(Adapter\AbstractAdapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		if($this->adapter instanceof Adapter\AbstractAdapter)
		{
			return $this->adapter;
		}

		throw new Exception\AdapterNotSetException;
	}

	public function getBlock($name, $locale)
	{
		if(isset($this->buffer[$name][$locale]))
		{
			return $this->buffer[$name][$locale];
		}

		return null;
	}

	public function setBlock($name, $locale, $content)
	{
		$block = new Block();

		$block->setData(array(
			'name' 		=> $name,
			'locale' 	=> $locale,
			'content' 	=> $content,
		));

		$this->buffer[$name][$locale] = $block;
		
		$this->getAdapter()->saveBlock($name, $locale, $content);
	}

	protected function buffer()
	{
		$blocks = $this->getAdapter()->fetchBlocks();
		$sorted = array();

		foreach($blocks as $block)
		{
			$sorted[$block->name][$block->locale] = $block;
		}

		$this->buffer = $sorted;
	}
}
?>