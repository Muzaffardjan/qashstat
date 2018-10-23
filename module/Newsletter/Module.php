<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Newsletter;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Pages\Tables\News;


class Module implements AutoloaderProviderInterface
{
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



        $eventManager->getSharedManager()->attach(
            'Pages\Tables\News', 
            News::EVENT_NEWS_ADDED, 
            array(
                $e->getApplication()->getServiceManager()->get('Newsletter\Controller\ManageController'),
                'send'
            )
        );
    }

     public function getServiceConfig()
    {
        return array(
            'abstract_factories' => array(),
            'aliases'            => array(),
            'factories'          => array
            (
                'Newsletter\Tables\Newsletter' => function($sm)
                {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Newsletter\ArrayObject\Newsletter());
                    $tableGateway = new TableGateway('newsletter', $dbAdapter, null, $resultSetPrototype);
                    $newsletter = new \Newsletter\Tables\Newsletter($tableGateway);
                    return $newsletter;
                },
            ),
        'invokables' => array(
            'Newsletter\Form\NewsletterForm'            => 'Newsletter\Form\NewsletterForm',
            'Newsletter\Controller\ManageController'    => 'Newsletter\Controller\ManageController',
            'Newsletter\Sender\Sender'                  => 'Newsletter\Sender\Sender',
        ),
        'services'   => array(),
        'shared'     => array(), 
        );
    }

    public function getViewHelperConfig()
    {
        return array(
           'invokables' => array(
              'newsletter' => 'Newsletter\View\Helper\Newsletter',
           ),
        );
    }
}
