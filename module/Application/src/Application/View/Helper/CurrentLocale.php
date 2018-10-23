<?php  
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CurrentLocale extends AbstractHelper
{
	protected $locale;

	public function __invoke()
	{
		return $this->getLocale();
	}

	public function getLocale()
	{
		if(!$this->locale)
		{
			$this->locale = $this->getView()->plugin('translate')->getTranslator()->getLocale();
		}

		return $this->locale;
	}
}	
?>