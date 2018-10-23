<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Controller\WeatherController;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();

        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 100);
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function(MvcEvent $event) {
            $viewModel = $event->getViewModel();
            $viewModel->setTemplate('layout/layout');
        }, -200);

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $config = $e->getApplication()->getConfig();

        // Setting fallback(default) locale
        if(isset($config['translator']))
        {
            $translatorConfig = $config['translator'];
            $translator = $e->getApplication()
                            ->getServiceManager()
                            ->get('translator')
                            ->getTranslator();

            if(isset($translatorConfig['locale']))
            {
                //$translator->setFallbackLocale($translatorConfig['locale']);
            }
        }

        $eventManager->attach(
            MvcEvent::EVENT_ROUTE, 
            function ($e) use ($config) {
                $path = $e->getTarget()->getRequest()->getUri()->getPath();
                $flag = false;

                foreach(array_keys($config['translator']['locales']) as $key)
                {
                    if(strpos($path, $key) !== false)
                    {
                        $flag = true;
                        break;
                    }
                }

                if(!$flag)
                {
                    header("Location: /404");
                }
            }, 
            -1
        );
    }

    public function preDispatch(MvcEvent $e)
    {
        $routeMatch         = $e->getRouteMatch();
        $routeMatchName     = $routeMatch->getMatchedRouteName();
        $requestParametres  = $e->getRouteMatch()->getParams();
        $translator         = $e->getApplication()
                                ->getServiceManager()
                                ->get('translator')
                                ->getTranslator();
        $locales            = $e->getApplication()->getServiceManager()->get(
            'config'
        )['translator']['locales'];
        $baseUrl            = $e->getApplication()->getRequest()->getBaseUrl();
        $currentUrl         = rtrim($e->getApplication()->getRequest()->getUri()->getPath(), '/');

        // Checking request for given locale parameter or redirect to home
        if((!isset($requestParametres['locale']) || !$requestParametres['locale'] || !isset($locales[$requestParametres['locale']])) && $routeMatchName && $baseUrl != $currentUrl)
        {
            header('Location: '. $baseUrl);
            exit;
        }
        elseif($baseUrl != $currentUrl)
        {
            $translator->setLocale($requestParametres['locale']);
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
           'invokables' => array(
              'currentLocale' => 'Application\View\Helper\CurrentLocale',
           ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
