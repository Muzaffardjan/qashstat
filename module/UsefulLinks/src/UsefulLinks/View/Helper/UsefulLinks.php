<?php  
namespace UsefulLinks\View\Helper;

use Zend\View\Helper\AbstractHelper;

class UsefulLinks extends AbstractHelper 
{
	protected $links;

	public function getServiceLocator()
	{
		return $this->getView()->getHelperPluginManager()->getServiceLocator();
	}

	public function getUsefulLinksTable()
	{
		return $this->getServiceLocator()->get('UsefulLinks\Table');
	}

	public function getLinks()
	{
		if(null === $this->links)
		{
			$this->links = array();

			foreach($this->getUsefulLinksTable()->getByLocale($this->getView()->currentLocale(), 'order_number ASC') as $link)
			{
				$this->links[] = $link;
			}
		}
		

		return $this->links;
	}

	public function __invoke($partial = null)
	{
		if($partial === null)
		{
			return $this;
		}

		return $this->getView()->partial($partial, array('links' => $this->getLinks()));
	}
}
?>