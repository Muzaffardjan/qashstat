<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Indexing;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Indexing\ZendSearch' => function($sm)
                {
                    $config = $sm->get('config');

                    if(!isset($config['indexing_zend_search']['storage']))
                    {
                        throw new \Exception('ZendSearch adapter config not found or invalid');
                    }

                    $indexing = new Indexing();

                    $indexing->setAdapter(new Adapter\ZendSearch($config['indexing_zend_search']['storage']));
                    $indexing->setResultSetObject(new ResultSet\ResultSet());
                    $indexing->getResultSetObject()->setResult(new Index\Index());
                    $indexing->getResultSetObject()->setAdapter(new ResultSet\Adapter\ZendSearch());

                    return $indexing;
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

        \ZendSearch\Lucene\Search\QueryParser::setDefaultEncoding('utf-8');
        \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
            new \ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive()
        );
    }
}
