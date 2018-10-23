<?php  
namespace Menu\Service;

use Zend\Navigation\Navigation;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class AbstractServiceFactory implements AbstractFactoryInterface
{
	/**
     * Top-level configuration key indicating navigation configuration
     *
     * @var string
     */
    const CONFIG_KEY = 'navigation';

    /**
     * Service manager factory prefix
     *
     * @var string
     */
    const SERVICE_PREFIX = 'Zend\Navigation\\';

    /**
     * Normalized name prefix
     */
    const NAME_PREFIX = 'zendnavigation';

    /**
     * Normalized name prefix
     */
    const MENU_NAME_PREFIX = 'menupositions';

    const LOCALE_SEPARATOR = '-';

    /**
     * Navigation configuration
     *
     * @var array
     */
    protected $config;

    protected $locale;

    /**
     * Can we create a navigation by the requested name?
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name Service name (as resolved by ServiceManager)
     * @param string $requestedName Name by which service was requested, must start with Zend\Navigation\
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (0 !== strpos($name, self::NAME_PREFIX) && 0 !== strpos($name, self::MENU_NAME_PREFIX)) {
            return false;
        }

        if(0 === strpos($name, self::NAME_PREFIX))
        {
        	$config = $this->getConfig($serviceLocator)[$this->getConfigName($name, $serviceLocator)];
        }

        if(0 === strpos($name, self::MENU_NAME_PREFIX))
        {
        	$config = $this->getPagesFromService($this->getConfigName($name, $serviceLocator), $serviceLocator);
        }

        return (!empty($config));
    }

    /**
     * Create a navigation container
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name Service name (as resolved by ServiceManager)
     * @param string $requestedName Name by which service was requested
     * @return Navigation
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
    	if(0 === strpos($name, self::NAME_PREFIX))
        {
        	$config = $this->getConfig($serviceLocator)[$this->getConfigName($name, $serviceLocator)];
        }

        if(0 === strpos($name, self::MENU_NAME_PREFIX))
        {
        	$config = $this->getPagesFromService($this->getConfigName($name, $serviceLocator), $serviceLocator);
        }
        
        $factory = new ConstructedNavigationFactory($config);
        return $factory->createService($serviceLocator);
    }

    /**
     * Get navigation configuration, if any
     *
     * @param  ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = array();
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config[self::CONFIG_KEY])
            || !is_array($config[self::CONFIG_KEY])
        ) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[self::CONFIG_KEY];
        return $this->config;
    }

    /**
     * Extract config name from service name
     *
     * @param string $name
     * @return string
     */
    protected function getConfigName($name, $serviceLocator)
    {
    	if(0 === strpos($name, self::NAME_PREFIX))
        {
        	return substr($name, strlen(self::NAME_PREFIX));
        }

        if(0 === strpos($name, self::MENU_NAME_PREFIX))
        {
            $config         = $serviceLocator->get('config');
            $locales        = array_keys($config['translator']['locales']);
            $localeLength   = 0;
            
            foreach($locales as $locale)
            {
                $temp         = preg_replace('/[^a-zA-Z]+/', '', strtolower($locale));
                
                if(false !== strpos($name, $temp))
                {
                    $this->locale = $locale;
                    $localeLength = strlen($temp);
                    break;
                }
            }

        	return substr(substr($name, strlen(self::MENU_NAME_PREFIX)), 0, $localeLength * -1).'/'.substr($name, $localeLength * -1, $localeLength);
        }
    }

    protected function getPagesFromService($id, $serviceLocator)
    {
    	$id 			= explode('/', $id);
		$locale 		= array_pop($id);
		//$locale 		= substr($locale, 0, 2).self::LOCALE_SEPARATOR.strtoupper(substr($locale, 2, 2));
		$config 		= $serviceLocator->get('config');
		$menuProvider 	= $serviceLocator->get('Menu\Provider');
		$position 		= current($id);
		$menu 			= $menuProvider->getConfig(array('position' => $position, 'locale' => $this->locale));
        
		return $menu;
    }
}
?>