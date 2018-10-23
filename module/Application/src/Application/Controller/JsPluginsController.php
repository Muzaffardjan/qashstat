<?php  
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class JsPluginsController extends AbstractActionController
{
	public function datatablesI18nAction()
	{
		$view = new \Zend\View\Model\ViewModel();

		$view->setTerminal(true);

		return $view; 
	}
}