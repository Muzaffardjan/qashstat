<?php  
namespace Menu\Adapter\DatabaseTable;

interface TableProviderInterface
{
	public function getMenu(array $options);
}
?>