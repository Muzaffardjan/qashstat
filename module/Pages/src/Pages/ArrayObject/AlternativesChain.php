<?php  
namespace Pages\ArrayObject;

class AlternativesChain implements \Pages\AlternativesChain\ChainedItemInterface
{
	public $chain;
	public $locale;
	public $id;

	public function exchangeArray($data)
	{
		if(!is_array($data) && !($data instanceof \Traversable))
			return;

		foreach($this->getData() as $property => $value)
		{
			if(isset($data[$property]))
			{
				$this->{$property} = $data[$property];
			}
		}
	}

	/* ChainedItemInterface interface methods */
	/**
	 * {@inheritDoc}
	 */
	public function getItemId()
	{
		return $this->id;
	}
	/**
	 *	{@inheritDoc}
	 */
	public function getItemLocale()
	{
		return $this->locale;
	}
	/**
	 * {@inheritDoc}
	 */
	public function getChainId()
	{
		return $this->chain;
	}
	/**
	 * {@inheritDoc}
	 */
	public function setChainId($chainId)
	{
		$this->chain = $chainId;
	}
	/* end ChainedItemInterface interface methods */

	public function flush()
	{
		$arrayCopy = $this->getData();

		foreach($arrayCopy as $key => $value)
		{
			$this->{$key} = null;
		}
	}

	/**
	 * Exchanges array with cleaning
	 *
	 * @param array $array
	 * @return Pages\ArrayObject\Page
	 */
	public function setData($array)
	{
		$this->flush();
		$this->exchangeArray($array);

		return $this;
	}

	public function getData()
	{
		return \Application\Library\Stdlib::get_public_vars($this);
	}
}
?>