<?php  
namespace Pages\View\Helper;

use Zend\View\Renderer\PhpRenderer;
use Zend\ServiceManager\ServiceManager;

interface PagesHelperPluginInterface
{
	public function setView(PhpRenderer $view);

	public function setServiceLocator(ServiceManager $sm);

	public function setPartial($partial);

	public function getPartial();

	public function render();
}
?>