<?php  
namespace Pages\AlternativesChain\Adapter\DatabaseTable;

interface TableProvider
{
	/**
	 * Gets chain by id 
	 * Returns array of Pages\AlternativesChain\ChainedItemInterface
	 *
	 * @return array
	 */
	public function getChain($chainId);
	/**
	 * Saves items chain
	 * $items array of Pages\AlternativesChain\ChainedItemInterface
	 *
	 * @param string $chainId
	 * @param array $items
	 */
	public function saveChain($chainId, array $items);
	/**
	 * Removes given items from chain by id
	 * 
	 * @param array $arrayItemsId
	 */
	public function deleteItemsFromChains(array $arrayItemsId);
}
?>