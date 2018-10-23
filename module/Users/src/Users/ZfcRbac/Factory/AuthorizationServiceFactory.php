<?php
namespace Users\ZfcRbac\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Users\ZfcRbac\Service\AuthorizationService;

/**
 * Factory to create the authorization service
 *
 * @author  Javlonbek Kahkramonov <kakjavlon@gmail.com>
 */
class AuthorizationServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return AuthorizationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var \Rbac\Rbac $rbac */
        $rbac = $serviceLocator->get('Rbac\Rbac');

        /* @var \ZfcRbac\Service\RoleService $roleService */
        $roleService = $serviceLocator->get('ZfcRbac\Service\RoleService');

        /* @var \ZfcRbac\Assertion\AssertionPluginManager $assertionPluginManager */
        $assertionPluginManager = $serviceLocator->get('ZfcRbac\Assertion\AssertionPluginManager');

        /* @var \ZfcRbac\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('ZfcRbac\Options\ModuleOptions');
        
        $authorizationService = new AuthorizationService($rbac, $roleService, $assertionPluginManager);
        $authorizationService->setAssertions($moduleOptions->getAssertionMap());
        $authorizationService->setSuperuser($serviceLocator->get('config')['super_user']);

        return $authorizationService;
    }
}
