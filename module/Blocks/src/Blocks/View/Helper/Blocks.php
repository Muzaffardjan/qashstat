<?php  
namespace Blocks\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Blocks extends AbstractHelper
{
	public function __invoke($name)
	{
		return $this->getBlock($name);
	}

	public function getBlock($name)
	{
		$blocks = $this->getServiceLocator()->get('Blocks');

		$block = $blocks->getBlock($name, $this->getView()->currentLocale());

		return is_object($block)? $block->content : '';
	}

	protected function getServiceLocator()
	{
		return $this->getView()->getHelperPluginManager()->getServiceLocator();
	}
}	
?>