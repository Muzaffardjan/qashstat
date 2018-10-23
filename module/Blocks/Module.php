<?php
namespace Blocks;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Blocks' => function($sm)
                {
                    $adapter = new Storage\Adapter\DatabaseTable
                    (
                        $sm->get('Zend\Db\Adapter\Adapter'),
                        'blocks'
                    );
                    $storage = new Storage\Storage($adapter);

                    return new Blocks($storage, $sm);
                }
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
           'invokables' => array(
              'block' => 'Blocks\View\Helper\Blocks',
           ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
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
