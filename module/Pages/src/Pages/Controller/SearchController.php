<?php  
namespace Pages\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Pages\Form\Search as SearchForm;
use Zend\Paginator\Adapter\ArrayAdapter as ArrayPaginator;
use Zend\Paginator\Paginator;

class SearchController extends AbstractActionController
{
	public function indexAction()
	{
		$form 	= new SearchForm();
		$sort 	= array('published' => 'Publish date');
		$types 	= array('page' => 'Page','event' => 'Event','news' => 'News');

		$form->get('sort')->setValueOptions($sort);
		$form->get('type')->setValueOptions($types);

		$form->setData($this->getRequest()->getQuery());

		if($form->isValid())
		{
			$formData 	= $form->getData();
			$tables 	= array(
				'news' 			=> $this->getServiceLocator()->get('Pages\Tables\News'),
				'events' 		=> $this->getServiceLocator()->get('Pages\Tables\Events'),
				'pages' 		=> $this->getServiceLocator()->get('Pages\Tables\Pages'),
				'categories' 	=> $this->getServiceLocator()->get('Pages\Tables\Categories'),
			);
			$indexing 	= $this->getServiceLocator()->get('Indexing\ZendSearch');

			$hits = array();
			
			foreach($indexing->find($formData['search']) as $hit)
			{
				switch($types)
				{
					case 'news': 
						if($hit->type != 'Pages\ArrayObject\News')
						{
							continue;
						}
					break;
					case 'event': 
						if($hit->type != 'Pages\ArrayObject\Event')
						{
							continue;
						}
					break;
					case 'page': 
						if($hit->type != 'Pages\ArrayObject\Page')
						{
							continue;
						}
					break;
				}

				if($this->params('locale') == $hit->locale)
				{
					$hits[$hit->type][] = $hit->page;
				}
			}

			$result = array();

			foreach($hits as $type => $items)
			{
				$resultset = array();

				if($type == 'Pages\ArrayObject\News')
				{
					$categories = array();

					foreach($tables['categories']->getWith(array('locale' => $this->params('locale'))) as $category)
					{
						$categories[$category->id] = $category;
					}
					
					foreach($tables['news']->getWith(array('id' => $items)) as $news)
					{
						$news->category = isset($categories[$news->category])? $categories[$news->category]: null;
						$result[] = $news;
					}

					continue;
				}
				elseif($type == 'Pages\ArrayObject\Event')
				{
					$resultset = $tables['events']->getWith(array('id' => $items));
				}
				else
				{
					$resultset = $tables['pages']->getWith(array('id' => $items));
				}

				foreach($resultset as $item)
				{
					$result[] = $item;
				}
			}

			$adapter 	= new ArrayPaginator($result);
			$paginator 	= new Paginator($adapter);

			$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
			$paginator->setItemCountPerPage((int)$this->params()->fromQuery('items', 5));

			return array(
				'form' 		=> $form,
				'posts' 	=> $paginator,
			);
		}

		return array(
			'form' => $form,
		);
	}
}
?>