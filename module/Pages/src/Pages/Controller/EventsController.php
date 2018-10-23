<?php
namespace Pages\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter as ArrayPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class EventsController extends AbstractActionController
{
	public function wallAction()
	{
		$tableEvents = $this->getServiceLocator()->get('Pages\Tables\Events');
		$events = array();

		foreach($tableEvents->fetchAll(null, 'happens DESC') as $event)
		{
			$events[] = $event;
		}

		$adapter 	= new ArrayPaginator($events);
		$paginator 	= new Paginator($adapter);

		return array(
			'posts' => $paginator,
		);
	}

	public function viewAction()
	{
		$tableEvents = $this->getServiceLocator()->get('Pages\Tables\Events');

		$event = $tableEvents->getByUrl($this->params('page'));

		if(!$event)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if($event->locale != $this->params('locale'))
		{
			if($event->getChainId())
			{
				$alternativesChain = $this->getServiceLocator()->get('Pages\AlternativesChain\EventsChain');
				$chain = $alternativesChain->getChain($event->getChainId());

				if(isset($chain[$this->params('locale')]) && $alternative = $tableEvents->getEventById($chain[$this->params('locale')]->id))
				{
					return $this->redirect()->toRoute('events/view', array('locale' => $this->params('locale'), 'page' => $alternative->url));
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
			'page' => $event,
		);
	}
}
?>