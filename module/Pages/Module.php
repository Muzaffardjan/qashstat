<?php
namespace Pages;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Pages\AlternativesChain\PagesChain' => function($sm)
                {
                    $alternativesChain = new \Pages\AlternativesChain\AlternativesChain();

                    $alternativesChain->setAdapter
                    (
                        new \Pages\AlternativesChain\Adapter\DatabaseTable
                        (
                            $sm->get('Pages\Tables\AlternativesChain'),
                            $sm->get('Pages\Tables\Pages')                            
                        )
                    );

                    return $alternativesChain;
                },
                'Pages\AlternativesChain\NewsChain' => function($sm)
                {
                    $alternativesChain = new \Pages\AlternativesChain\AlternativesChain();

                    $alternativesChain->setAdapter
                    (
                        new \Pages\AlternativesChain\Adapter\DatabaseTable
                        (
                            $sm->get('Pages\Tables\AlternativesChain'),
                            $sm->get('Pages\Tables\News')                            
                        )
                    );

                    return $alternativesChain;
                },
                'Pages\AlternativesChain\EventsChain' => function($sm)
                {
                    $alternativesChain = new \Pages\AlternativesChain\AlternativesChain();

                    $alternativesChain->setAdapter
                    (
                        new \Pages\AlternativesChain\Adapter\DatabaseTable
                        (
                            $sm->get('Pages\Tables\AlternativesChain'),
                            $sm->get('Pages\Tables\Events')                            
                        )
                    );

                    return $alternativesChain;
                },
                'Pages\Tables\Pages' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Page());
                    return new Tables\Pages(new \Zend\Db\TableGateway\TableGateway('pages', $adapter, null, $resultSetPrototype), $sm);
                },
                'Pages\Tables\Categories' => function($sm) {
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Category());

                    return new Tables\Categories(
                        new \Zend\Db\TableGateway\TableGateway(
                            'categories',
                            $sm->get('Zend\Db\Adapter\Adapter'),
                            null,
                            $resultSetPrototype
                        ),
                        $sm
                    );
                },
                'Pages\Tables\News' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\News());
                    return new Tables\News(new \Zend\Db\TableGateway\TableGateway('news', $adapter, null, $resultSetPrototype), $sm);
                },
                'Pages\Tables\Events' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Event());
                    return new Tables\Events(new \Zend\Db\TableGateway\TableGateway('events', $adapter, null, $resultSetPrototype), $sm);
                },
                'Pages\Tables\AlternativesChain' => function($sm)
                {
                    $adapter            = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\AlternativesChain());
                    return new Tables\AlternativesChain(new \Zend\Db\TableGateway\TableGateway('alternatives_chain', $adapter, null, $resultSetPrototype));
                },
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

    public function getViewHelperConfig()
    {
        return array(
           'invokables' => array(
              'pages' => 'Pages\View\Helper\Pages',
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
