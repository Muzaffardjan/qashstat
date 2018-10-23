<?php
namespace Pages\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PageController extends AbstractActionController
{
	public function viewAction()
	{
		$tablePages = $this->getServiceLocator()->get('Pages\Tables\Pages');

		$page = $tablePages->getByUrl($this->params('page'));

		if(!$page || (!$page->visible && !$this->identity()))
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if($page->locale != $this->params('locale'))
		{
			if($page->getChainId())
			{
				$alternativesChain = $this->getServiceLocator()->get('Pages\AlternativesChain\PagesChain');
				$chain = $alternativesChain->getChain($page->getChainId());

				if(isset($chain[$this->params('locale')]) && $alternative = $tablePages->getPageById($chain[$this->params('locale')]->id))
				{
					return $this->redirect()->toRoute('page/view', array('locale' => $this->params('locale'), 'page' => $alternative->url));
				}
				else
				{
					$this->getResponse()->setStatusCode(404);
					return;
				}
			}
			else
			{
				$this->getResponse()->setStatusCode(404);
				return;
			}
		}

		return array(
			'page' => $page,
		);
	}
}
?>