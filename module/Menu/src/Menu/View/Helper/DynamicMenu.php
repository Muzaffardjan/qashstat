<?php  
namespace Menu\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DynamicMenu extends AbstractHelper
{
	protected $toUnderScore;

	protected $toLower;

	public function __invoke($id)
	{
		$container 	= $this->getContainer($id);
		$config 	= $this->getServiceLocator()->get('config');
		$filter 	= new \Zend\Filter\Word\UnderscoreToCamelCase();

		$config['navigation'][strtolower($filter->filter(key($container)))] = current($container);

		$this->getServiceLocator()->setAllowOverride(true);
		$this->getServiceLocator()->setService('Config', $config);

		//return $this->getView()->navigation('Zend\Navigation\\'.ucfirst(strtolower($filter->filter(key($container)))));
	}

	public function getContainer($id)
	{
		$keys 		= explode('\\', $id);
		$locale 	= array_pop($keys);
		$config 	= $this->getServiceLocator()->get('config');
		$tableMenu 	= $this->getServiceLocator()->get('Menu\Tables\Menu');

		foreach($keys as $key => $value)
		{
			$keys[$key] = $this->filter($value);
		}

		$menuConfig = $config;

		foreach($keys as $key)
		{
			if(isset($menuConfig[$key]))
			{
				$menuConfig = $menuConfig[$key];
			}
			else
			{
				throw new \Exception('Menu: \''.$id.'\' config not found');
			}
		}

		$position 	= $keys[count($keys) - 1];
		$menu 		= $tableMenu->getWith(array('position' => $position, 'locale' => $locale));
		
		return array($position => $menu->current()->getContainer());
	}

	protected function getServiceLocator()
	{
		return $this->getView()->getHelperPluginManager()->getServiceLocator();
	}

	protected function filter($id)
	{
		if(!$this->toUnderScore)
		{
			$this->toUnderScore = new \Zend\Filter\Word\CamelCaseToUnderscore();
		}

		if(!$this->toLower)
		{
			$this->toLower = new \Zend\Filter\StringToLower();
		}

		return $this->toLower->filter($this->toUnderScore->filter($id));
	}
}	
?>