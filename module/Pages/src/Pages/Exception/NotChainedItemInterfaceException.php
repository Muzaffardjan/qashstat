<?php  
namespace Pages\Exception;

class NotChainedItemInterfaceException
{
    /**
     * @var string
     */
    protected $message = 'Given items array must have only items type of Pages\AlternativesChain\ChainedItemInterface';
}
?>