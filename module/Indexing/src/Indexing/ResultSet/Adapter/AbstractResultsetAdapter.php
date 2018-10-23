<?php  
namespace Indexing\ResultSet\Adapter;

abstract class AbstractResultsetAdapter
{
	/**
	 * Gets array form of search result
	 * 
	 * @return array
	 */
	abstract public function asArray($searchResult);
}
?>