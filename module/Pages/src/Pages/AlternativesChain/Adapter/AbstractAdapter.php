<?php  
namespace Pages\AlternativesChain\Adapter;

use Pages\Exception\SameLocaleInChainException;

abstract class AbstractAdapter
{
	protected $chainIdPrefix = 'chain_';
	/**
	 * Generates unique chain id
	 *
	 * @return string
	 */
	public function generateChainId()
	{
		return $this->chainIdPrefix . sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
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
	/**
	 * $items array of Pages\AlternativesChain\ChainedItemInterface
	 * that contains min 2 elements 
	 * Returns array of chained to each other items
	 *
	 * @param array $items
	 * @return array
	 */
	public function chain(array $items)
	{
		$items 	= $this->filter($items);
		$flag 	= null;

		foreach($items as $item)
		{
			if($flag = $item->getChainId())
			{
				break;
			}
		}

		if($flag)
		{
			$chainId = $flag;
		}
		else
		{
			$chainId = $this->generateChainId();
		}

		foreach($items as $item)
		{
			$item->setChainId($chainId);
		}

		return $items;
	}

	public function filter(array $items)
	{
		$collection = array();

		foreach($items as $item)
		{
			if(isset($collection[$item->getItemLocale()]))
			{
				throw new SameLocaleInChainException;
			}

			$collection[$item->getItemLocale()] = $item;
		}

		return $collection;
	}
	/**
	 * Gets alternative items in chain
	 *
	 * @param string $chainId
	 * @return array Pages\AlternativesChain\ChainedItemInterface
	 */
	abstract public function getChain($chainId);
	/**
	 * Changes chains if needed
	 *
	 * @param ChainedItemInterface $item 
	 */
	abstract public function itemLocaleChanged(\Pages\AlternativesChain\ChainedItemInterface $item);

	/**
	 * Deletes item from chain
	 * Changes chains if needed
	 *
	 * @param ChainedItemInterface $item 
	 */
	abstract public function itemDeleted(\Pages\AlternativesChain\ChainedItemInterface $item);
}
?>