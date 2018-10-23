<?php
namespace Users;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getServiceConfig() 
    {
      return array(
            'aliases'   => array(
                'Zend\Authentication\AuthenticationService' => 'Users\Authentication\AuthenticationService',
                //'ZfcRbac\Service\AuthorizationService' => 'Users\Authentication\AuthenticationService',
            ),
            'factories' => array(
                'ZfcRbac\Service\AuthorizationService' => 'Users\ZfcRbac\Factory\AuthorizationServiceFactory',
                'Users\Authentication\RbacListener' => 'Users\Factory\RbacListenerFactory',
                'Users\Authentication\AuthenticationService'=> function($sm)
                {
                    $adapter     = new Authentication\Adapter\Table(null, null, $sm->get('Users\Tables\Control'));
                    //$AuthenticationService  = new \Zend\Authentication\AuthenticationService();
                    $AuthenticationService = new Authentication\AuthenticationService();
                    $AuthenticationService->setAdapter($adapter);
                    $AuthenticationService->setStorage(new Authentication\Storage\Storage());

                    return $AuthenticationService;
                },
                'Users\Tables\Control' => function($sm)
                {
                    $tablesControl = new Tables\Control();

                    if(!($tablesControl instanceof \Application\Db\TablegatewayObjectInterface))
                    {
                        throw new \Exception('TablegatewayObjectInterface needed!');
                    }
                    
                    $DbAdapter          = $sm->get("Zend\Db\Adapter\Adapter");
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Control());
                    $tablesControl->setTablegateway(new \Zend\Db\TableGateway\TableGateway('control', $DbAdapter, null, $resultSetPrototype));
                    
                    return $tablesControl;
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager           = $e->getApplication()->getEventManager();
        $sharedEventManager     = $eventManager->getSharedManager();
        $serviceManager         = $e->getApplication()->getServiceManager();
        $rbacListener           = $serviceManager->get('Users\Authentication\RbacListener');

        // Attach Zfc RedirectStrategy
        $eventManager->attach(
            $e->getTarget()->getServiceManager()->get('ZfcRbac\View\Strategy\RedirectStrategy')
        );

        // Zend\Navigation isAllowed event attach
        // Hides elements in navigation that not allowed to user
        $eventManager->getSharedManager()->attach(
            'Zend\View\Helper\Navigation\AbstractHelper', 
            'isAllowed', 
            array($rbacListener, 'accept')
        );

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
