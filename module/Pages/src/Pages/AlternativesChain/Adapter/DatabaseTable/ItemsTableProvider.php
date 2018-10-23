<?php  
namespace Pages\AlternativesChain\Adapter\DatabaseTable;

interface ItemsTableProvider
{
	/**
	 * Saves state of chained items
	 * $items array of Pages\AlternativesChain\ChainedItemInterface
	 *
	 * @param array $items
	 */
	public function saveState(array $items);
}
?>