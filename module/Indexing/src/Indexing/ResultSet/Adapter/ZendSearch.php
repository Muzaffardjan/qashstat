<?php  
namespace Indexing\ResultSet\Adapter;

use ZendSearch\Lucene\Search\QueryHit;

class ZendSearch extends AbstractResultsetAdapter
{
	/**
	 * {@inheritDoc}
	 */
	public function asArray($searchResult)
	{
		if(!$searchResult instanceof QueryHit)
		{
			throw new Exception\InvalidArgument('Argument must be instance of ZendSearch\Lucene\Search\QueryHit');
		}

		$array 			= array();
		$array['id'] 	= $searchResult->id;
		$document 		= $searchResult->getDocument();

		foreach($document->getFieldNames() as $key)
		{
			$array[$key] = $document->getFieldValue($key);
		}

		return $array;
	}
}
?>