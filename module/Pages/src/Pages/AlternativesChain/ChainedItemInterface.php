<?php 
namespace Pages\AlternativesChain;

interface ChainedItemInterface
{
	/**
	 * Gets unique id of chain item
	 *
	 * @return string|int
	 */
	public function getItemId();
	/**
	 * Gets locale of chain item
	 *
	 * @return string|int
	 */
	public function getItemLocale();
	/**
	 * Gets chain id if object has chain
	 *
	 * @return string|null
	 */
	public function getChainId();
	/**
	 * Sets chain id
	 *
	 * @return void
	 */
	public function setChainId($chainId);
}
?>