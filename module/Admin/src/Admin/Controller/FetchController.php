<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class FetchController extends AbstractActionController
{
	public function categoriesAction()
	{
		if(!$this->getRequest()->isPost())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if(!$this->getRequest()->getPost('locale'))
		{
			return new JsonModel([]);
		}

		$tableCategories = $this->getServiceLocator()->get('Pages\Tables\Categories');
		$categories = array();

		foreach($tableCategories->getWith(array('locale' => $this->getRequest()->getPost('locale'))) as $category)
		{
			$categories[] = array(
				'label'  => $category->title,
				'route'	 => 'blog/default',
				'params' => array(
					'locale' 	=> $category->locale,
					'category' 	=> $category->url,
				),
			);
		}

		return new JsonModel($categories);
	}

	public function pagesAction()
	{
		if(!$this->getRequest()->isPost())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if(!$this->getRequest()->getPost('locale') || !$this->getRequest()->getPost('query'))
		{
			return new JsonModel([]);
		}

		$tablePages = $this->getServiceLocator()->get('Pages\Tables\Pages');
		$pages 		= array();
		$ids 		= array();
		$indexer 	= $this->getServiceLocator()->get('Indexing\ZendSearch');
		$query 		= $this->getRequest()->getPost('query');

		if(count(explode(' ', $query)) == 1)
		{
			$query .= '~';
		}
		
		foreach($indexer->find($query) as $hit)
		{
			if($hit->type != 'Pages\ArrayObject\Page')
			{
				continue;
			}

			$ids[] = $hit->page;
		}

		if($ids)
		{
			foreach($tablePages->getWith(array('id' => $ids)) as $page)
			{
				$pages[] = array(
					'label'		=> $page->title,
					'route' 	=> 'page/view',
					'params' 	=> array(
						'locale' 	=> $page->locale,
						'page'		=> $page->url,
					),
				);
			}
		}

		return new JsonModel($pages);
	}

	public function eventsAction()
	{
		if(!$this->getRequest()->isPost())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if(!$this->getRequest()->getPost('locale') || !$this->getRequest()->getPost('query'))
		{
			return new JsonModel([]);
		}

		$tableEvents 	= $this->getServiceLocator()->get('Pages\Tables\Events');
		$events 		= array();
		$ids 			= array();
		$indexer 		= $this->getServiceLocator()->get('Indexing\ZendSearch');
		$query 			= $this->getRequest()->getPost('query');

		if(count(explode(' ', $query)) == 1)
		{
			$query .= '~';
		}
		
		foreach($indexer->find($query) as $hit)
		{
			if($hit->type != 'Pages\ArrayObject\Event')
			{
				continue;
			}

			$ids[] = $hit->page;
		}

		if($ids)
		{
			foreach($tableEvents->getWith(array('id' => $ids)) as $event)
			{
				$events[] = array(
					'label'		=> $event->title,
					'route' 	=> 'events/view',
					'params' 	=> array(
						'locale' 	=> $event->locale,
						'page'		=> $event->url,
					),
				);
			}
		}

		return new JsonModel($events);
	}

	public function bySearchAction()
	{
		if(!$this->getRequest()->isPost() || !$this->getRequest()->getPost('query'))
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$indexer 	= $this->getServiceLocator()->get('Indexing\ZendSearch');
		$query 		= $this->getRequest()->getPost('query');

		if(strpos($query, '-') === false && strpos($query, '_') === false && strpos($query, ' ') === false)
		{
			$query .= '~';
		}

		$hits 		= $indexer->find($query);
		$tableCategories = $this->getServiceLocator()->get('Pages\Tables\Categories');
		$result 	= array();
		$categories = array();
		$tables 	= array(
			'pages' 	=> $this->getServiceLocator()->get('Pages\Tables\Pages'),
			'news' 		=> $this->getServiceLocator()->get('Pages\Tables\News'),
			'events' 	=> $this->getServiceLocator()->get('Pages\Tables\Events'),
		); 

		foreach($tableCategories->fetchAll() as $category)
		{
			$categories[$category->id] = $category;
		}

		foreach($hits as $hit)
		{
			switch($hit->type)
			{
				case 'Pages\ArrayObject\Page':
					$result['pages'][] = $hit->page;
				break;
				case 'Pages\ArrayObject\News':
					$result['news'][] = $hit->page;
				break;
				case 'Pages\ArrayObject\Event':
					$result['events'][] = $hit->page;
				break;
			}
		}

		$hits = array();

		foreach($result as $key => $value)
		{
			if(!$value)
			{
				continue;
			}

			$hits[$key] = $tables[$key]->getWith(array('id' => $value));
		}

		$result 	= array();
		$current 	= array();

		foreach($hits as $type => $resultSet)
		{
			foreach($resultSet as $item)
			{
				$current['title'] = $item->title;

				switch($type)
				{
					case 'pages': 
						$current['type'] = 'page';
						$current['url'] = $this->url()->fromRoute('page/view', array('locale' => $item->locale, 'page' => $item->url));
					break;
					case 'news': 
						$current['type'] = 'news';
						$current['url'] = $this->url()->fromRoute('news/view', array('locale' => $item->locale, 'category' => $categories[$item->category]->url, 'year' => date('Y', $item->added), 'month' => date('m', $item->added), 'date' => date('d', $item->added), 'page' => $item->url));
					break;
					case 'events': 
						$current['type'] = 'event';
						$current['url'] = $this->url()->fromRoute('events/view', array('locale' => $item->locale, 'page' => $item->url));
					break;
				}

				$current['url'] = ltrim(str_replace($this->getRequest()->getBaseUrl(), '', $current['url']), '/');

				$result[] = $current;
			}
		}

		return new JsonModel($result);
	}
}
?>