<?php
namespace Menu;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Menu\Tables\Menu' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Menu());
                    return new Tables\Menu(new \Zend\Db\TableGateway\TableGateway('menu', $adapter, null, $resultSetPrototype), $sm);
                },
                'Menu\Provider' => function($sm)
                {
                    $adapter = new Adapter\DatabaseTable($sm->get('Menu\Tables\Menu'));

                    return new Provider($adapter);
                },
            ),
            'abstract_factories' => array(
                'Zend\Navigation\Service\NavigationAbstractServiceFactory' => 'Menu\Service\AbstractServiceFactory',
            ),
        );
    }

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

    public function getViewHelperConfig()
    {
        return array(
           'invokables' => array(
              'dynamicMenu' => 'Menu\View\Helper\DynamicMenu',
           ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
