<?php  
namespace Pages\AlternativesChain\Adapter;

use Pages\AlternativesChain\Adapter\DatabaseTable\TableProvider;
use Pages\AlternativesChain\Adapter\DatabaseTable\ItemsTableProvider;

class DatabaseTable extends AbstractAdapter
{
	/**
	 * @var Pages\AlternativesChain\Adapter\DatabaseTable\TableProvider
	 */
	protected $tableProvider;
	/**
	 * @var Pages\AlternativesChain\Adapter\DatabaseTable\ItemsTableProvider
	 */
	protected $itemsTableProvider;

	public function __construct(TableProvider $tableProvider = null, ItemsTableProvider $itemsTableProvider = null)
	{
		if($tableProvider)
		{
			$this->setTableProvider($tableProvider);
		}

		if($itemsTableProvider)
		{
			$this->setItemsTableProvider($itemsTableProvider);
		}
	}

	public function setTableProvider(TableProvider $tableProvider)
	{
		$this->tableProvider = $tableProvider;
	}

	public function getTableProvider()
	{
		return $this->tableProvider;
	}

	public function setItemsTableProvider(ItemsTableProvider $itemsTableProvider)
	{
		$this->itemsTableProvider = $itemsTableProvider;
	}

	public function getItemsTableProvider()
	{
		return $this->itemsTableProvider;
	}
	/**
	 * {@inheritDoc}
	 */
	public function chain(array $items)
	{
		$items 			= parent::chain($items);
		$arrayItemsId 	= array();
		$chainId 		= null;

		foreach($items as $item)
		{
			$chainId 		= $item->getChainId();
			$arrayItemsId[] = $item->getItemId();
		}

		$oldItems = $this->getChain($chainId);
		$conflicts = array();

		foreach($oldItems as $item)
		{	
			if(!$item instanceof \Pages\AlternativesChain\ChainedItemInterface)
			{
				throw new \Exception('TableProvider getChain() must return array of Pages\AlternativesChain\ChainedItemInterface');
			}

			if(isset($items[$item->getItemLocale()]) && !in_array($item->getItemId(), $arrayItemsId))
			{
				$arrayItemsId[] = $item->getItemId();
				$item->setChainId(null);

				$conflicts[] = $item;
			}
		}

		$this->getTableProvider()->deleteItemsFromChains($arrayItemsId);
		$this->getTableProvider()->saveChain($chainId, $items);

		if($conflicts)
		{
			$items = array_merge($conflicts, $items);
		}

		$this->getItemsTableProvider()->saveState($items);
		
		return $items;
	}

	/**
	 * Gets chain by id
	 *
	 * @return array
	 */
	public function getChain($chainId)
	{
		$chain = $this->getTableProvider()->getChain($chainId);
		$result = array();

		foreach($chain as $item)
		{
			$result[$item->getItemLocale()] = $item;
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function itemLocaleChanged(\Pages\AlternativesChain\ChainedItemInterface $item)
	{
		$items = $this->getChain($chainId);
		
		if(isset($items[$item->getLocale()]) && $item->getItemId() !== $items[$item->getLocale()]->getItemId())
		{
			$item->setChainId(null);

			$this->getTableProvider()->deleteItemsFromChains(array($item->getItemId()));
			$this->getItemsTableProvider()->saveState(array($item));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function itemDeleted(\Pages\AlternativesChain\ChainedItemInterface $item)
	{
		$item->setChainId(null);
		$this->getTableProvider()->deleteItemsFromChains(array($item->getItemId()));
		$this->getItemsTableProvider()->saveState(array($item));
	}
}
?>