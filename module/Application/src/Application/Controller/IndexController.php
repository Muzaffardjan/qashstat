<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return array();
    }

    public function redirectAction()
    { 
        $config = $this->getServiceLocator()->get('config');
        $set    = $config['translator']['locale'];

        if (($acceptLanguage = $this->getRequest()->getHeaders('Accept-Language'))) {
            $byPriority = $acceptLanguage->getPrioritized();

            if (is_array($byPriority) && count($byPriority)) {
                $prior = current($byPriority);
                $prior = $prior->getLanguage();
                $have  = array_keys($config['translator']['locales']);

                if (!in_array($prior, $have)) {
                    foreach ($byPriority as $locale) {
                        if (in_array($locale, $have)) {
                            $set = $locale;
                        }
                    }
                }
                else {
                    $set = $prior;
                }
            }
        }

        if ($this->getRequest()->getQuery('redirectTo')) {
            $request  = $this->getRequest();
            $request->setUri($this->getRequest()->getQuery('redirectTo'));

            if ($routeToBeMatched = $this->getServiceLocator()
                    ->get('Router')
                    ->match($request)) {
                $params = $routeToBeMatched->getParams();

                if (isset($params['locale'])) {
                    $route = $routeToBeMatched->getMatchedRouteName();

                    foreach ($config['route_aliases'] as $key => $value) {
                        if (preg_match('/'.$key.'/', $route)) {
                            $route = $value;
                            break;
                        }
                    }

                    return $this->redirect()->toRoute(
                        $route, 
                        ['locale' => $params['locale']]
                    );
                }
            }
        }

        return $this->redirect()->toRoute(
            'home-locale', 
            ['locale' => $set]
        );
    }
}
