<?php  
namespace Newsletter\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Newsletter 
	  extends AbstractHelper
{
	/**
	 * @var ServiceLocatorInterface $services Application service manager
	 */
	protected $services;

	/**
	 * Gets ServiceLocator
	 *
	 * @return ServiceManagerInterface
	 */
	public function getServiceLocator()
	{
		return $this->getView()->getHelperPluginManager()->getServiceLocator();
	}

	/**
	 * Gets newsletter form
	 *
	 * @return Newsletter\Form\NewsletterForm
	 */
	public function getNewsletterForm()
	{
		return $this->getServiceLocator()->get('Newsletter\Form\NewsletterForm');
	}

	/**
	 * Invoke
	 */
	public function __invoke($partial = null)
	{
		if(null === $partial)
		{
			return $this;
		}

		return $this->getView()->partial($partial, array('form' => $this->getNewsletterForm()));
	}
}
?>