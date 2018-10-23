<?php  
namespace Pages\AlternativesChain;

use Pages\AlternativesChain\Adapter\AbstractAdapter;
use Pages\Exception\NotChainedItemInterfaceException;
use Pages\Exception\AdapterNotSetException;

class AlternativesChain
{
	/**
	 * @var Pages\AlternativesChain\Adapter\AbstractAdapter
	 */
	protected $adapter;

	/**
	 * Sets adapter
	 *
	 * @param Pages\AlternativesChain\Adapter\AbstractAdapter $adapter
	 * @return AlternativesChain
	 */
	public function setAdapter(AbstractAdapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	/**
	 * Gets adapter
	 *
	 * @return Pages\AlternativesChain\Adapter\AbstractAdapter
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Chain given array of ChainedItemInterface
	 *
	 * @param array $items
	 * @throws AdapterNotSetException if adapter not set
	 * @throws NotChainedItemInterfaceException 
	 * if $items contain item other than Pages\AlternativesChain\ChainedItemInterface
	 */
	public function chain(array $items)
	{	
		if(!$this->getAdapter())
		{
			throw new AdapterNotSetException;
		}

		if(count($items) < 2)
		{
			throw new \Exception('Items count must be at least 2');
		}

		foreach($items as $item)
		{
			if(!($item instanceof ChainedItemInterface))
			{
				throw new NotChainedItemInterfaceException;
			}
		}

		$this->getAdapter()->chain($items);
	}

	/**
	 * Gets chain by id
	 *
	 * @return array
	 */
	public function getChain($chainId)
	{
		return $this->getAdapter()->getChain($chainId);
	}

	/**
	 * Changes chains if needed
	 *
	 * @param ChainedItemInterface $item 
	 */
	public function itemLocaleChanged(ChainedItemInterface $item)
	{
		$this->getAdapter()->itemLocaleChanged($item);
	}

	/**
	 * Deletes item if exists
	 * Changes chains if needed
	 *
	 * @param ChainedItemInterface $item 
	 */
	public function itemDeleted(ChainedItemInterface $item)
	{
		$this->getAdapter()->itemDeleted($item);
	}
}
?>