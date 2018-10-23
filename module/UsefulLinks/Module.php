<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace UsefulLinks;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

use UsefulLinks\Links\Link;
use UsefulLinks\Links\Hydrator;
use UsefulLinks\Table;

class Module implements AutoloaderProviderInterface
{
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'UsefulLinks\Table' => function($sm) {
                    // Get config of a table
                    $tableConfig    = $sm->get('config')['useful_links']['table'];
                    // Get Db adapter
                    $adapter        = $sm->get('Zend\Db\Adapter\Adapter');
                    // Hydrating resultset
                    $resultSetPrototype = new HydratingResultSet();
                    // Set hydrator to prototype
                    $resultSetPrototype->setHydrator($sm->get('UsefulLinks\Links\Hydrator'));
                    $resultSetPrototype->setObjectPrototype(new Link());
 
                    $tableGateway       = new TableGateway($tableConfig['name'], $adapter, null, $resultSetPrototype);
 
                    return new Table($tableGateway);
                },
                'UsefulLinks\Links\Hydrator' => function($sm){
                    // Get config of a table
                    $tableConfig    = $sm->get('config')['useful_links']['table'];
                    // Hydrator itself
                    $hydrator           = new Hydrator();
                    // Set map to hydrator
                    $hydrator->setNamingStrategy(new MapNamingStrategy($tableConfig['hydrator_map']));
                    
                    return $hydrator;
                },
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
              'usefulLinks' => 'UsefulLinks\View\Helper\UsefulLinks',
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
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
