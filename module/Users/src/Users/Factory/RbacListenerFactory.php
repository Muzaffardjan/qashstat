<?php
 
namespace Users\Factory;
 
use Users\Authentication\RbacListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
class RbacListenerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authorizationService = $serviceLocator->get('ZfcRbac\Service\AuthorizationService');
 
        return new RbacListener($authorizationService);
    }
}