<?php 
namespace Pages\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PagesTablegatewayFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
    { 
        return new \Pages\Tables\Pages($authorizationService);
    }
}
?>