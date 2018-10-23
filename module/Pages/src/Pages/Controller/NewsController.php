<?php
namespace Pages\Controller;

use Pages\Tables\Categories;
use Pages\Tables\News;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Feed\Writer\Feed;
use Zend\View\Model\FeedModel;

class NewsController extends AbstractActionController
{
	public function viewAction()
	{
        /**
         * @var Response $response
         */
	    $response = $this->getResponse();

        /**
         * @var News $tableNews
         */
		$tableNews = $this->getServiceLocator()->get('Pages\Tables\News');

        /**
         * @var Categories $tableCategories
         */
		$tableCategories = $this->getServiceLocator()->get('Pages\Tables\Categories');

		$category = $tableCategories->getWith(array('url' => $this->params('category')))->current();

		if (! $category) {
			$response->setStatusCode(404);
			return;
		}

		$spec = [
			'id' => $this->params('id'),
			'category' => $category->id,
        ];

		$anews = $tableNews->getWith(
		    function($select) use($spec) {
			    $select->where(
                    function($where) use($spec) {
                        $where->equalTo('id', $spec['id'])->equalTo('category', $spec['category']);
                    }
                );
		    }
        )->current();

		if (! $anews) {
			$response->setStatusCode(404);
			return;
		}

		if ($anews->locale != $this->params('locale')) {
			if ($anews->getChainId()) {
				$alternativesChain 	= $this->getServiceLocator()->get('Pages\AlternativesChain\NewsChain');
				$chain 				= $alternativesChain->getChain($page->getChainId());

				if(isset($chain[$this->params('locale')]) && $alternative = $tablePages->getNewsById($chain[$this->params('locale')]->id))
				{
					return $this->redirect()->toRoute('pages/page', array('locale' => $this->params('locale'), 'action' => 'view', 'page' => $alternative->url));
				}
				else
				{
					$this->getResponse()->setStatusCode(404);
					return;
				}
			} else {
				$response->setStatusCode(404);
				return;
			}
		}

		return new ViewModel([
            'news' => $anews,
            'category' => $category,
        ]);
	}

	public function rssAction()
	{
		$tableNews 			= $this->getServiceLocator()->get('Pages\Tables\News');
		$tableCategories 	= $this->getServiceLocator()->get('Pages\Tables\Categories');
		$translator			= $this->getServiceLocator()->get('translator');
        $anews 				= array();
        $categories 		= array();

        foreach($tableNews->fetchAll(array('locale' => $this->params('locale'))) as $news)
        {
        	$anews[] = $news;
        }

        foreach($tableCategories->fetchAll() as $category)
        {
        	$categories[$category->id] = $category;
        }

		$feed = new Feed();
        $feed->setTitle($translator->translate('Project name'));
        $feed->setFeedLink($this->getRequest()->getUri()->toString(), 'atom');
        $feed->setGenerator('Foreach.Soft Mod355v2');
        $feed->addAuthor(array(
            'name'  => 'Foreach.Soft',
            'uri'   => 'http://foreach.uz',
        ));
        $feed->setDescription(sprintf($translator->translate('RSS feed of %s'), $translator->translate('Project name')));
        $feed->setLink($this->url()->fromRoute(null, array('locale' => $this->params('locale'))));

        if(isset($anews[0]))
        {
        	$feed->setDateModified((int)$anews[0]->added);
        }
        else
        {
        	$feed->setDateModified(0);
        }

        foreach($anews as $news)
        {
            //create entry...
            $entry = $feed->createEntry();
            $entry->setTitle($news->title);
            $entry->setLink
            (
            	$this->url()
            	->fromRoute
            	(
            		'news/view', 
            		array(
            			'locale' 	=> $this->params('locale'),
            			'category' 	=> (isset($categories[$news->category])?$categories[$news->category]->url:''), 
            			'year' 		=> date('Y', $news->added), 
            			'month' 	=> date('m', $news->added), 
            			'date' 		=> date('d', $news->added), 
            			'page' 		=> $news->url,
            		),
            		array(
            			'force_canonical' => true,
            		)
            	)
            );

            $entry->setDescription($news->description);
 
            $entry->setDateModified((int)$news->added);
            $entry->setDateCreated((int)$news->added);
 
            $feed->addEntry($entry);
        }
 
        $feed->export('rss');
 
        $feedmodel = new FeedModel();
        $feedmodel->setFeed($feed);
 
        return $feedmodel;
	}
}
?>