<?php

namespace Media;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Media\Tables\Videos' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Video());
                    return new Tables\Videos(new \Zend\Db\TableGateway\TableGateway('videos', $adapter, null, $resultSetPrototype), $sm);
                },
                'Media\Tables\VideoDescriptions' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\VideoDescription());
                    return new Tables\VideoDescriptions(new \Zend\Db\TableGateway\TableGateway('video_description', $adapter, null, $resultSetPrototype), $sm);
                },
                'Media\Tables\Galleries' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Gallery());
                    return new Tables\Galleries(new \Zend\Db\TableGateway\TableGateway('galleries', $adapter, null, $resultSetPrototype), $sm);
                },
                'Media\Tables\ImageCollection' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Image());
                    return new Tables\ImagesCollection(new \Zend\Db\TableGateway\TableGateway('images_collection', $adapter, null, $resultSetPrototype), $sm);
                },
                'Media\Tables\ImageDescriptions' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\ImageDescription());
                    return new Tables\ImageDescriptions(new \Zend\Db\TableGateway\TableGateway('image_description', $adapter, null, $resultSetPrototype), $sm);
                },
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
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
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
