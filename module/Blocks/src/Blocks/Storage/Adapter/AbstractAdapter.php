<?php  
namespace Blocks\Storage\Adapter;

abstract class AbstractAdapter
{
	abstract public function fetchBlocks();

	abstract public function saveBlock($name, $locale, $content);
}
?>