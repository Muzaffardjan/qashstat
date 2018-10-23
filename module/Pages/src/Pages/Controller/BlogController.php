<?php  
namespace Pages\Controller;

use Pages\Tables\Categories;
use Pages\Tables\News;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter as ArrayPaginator;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Where;
use Zend\View\Model\ViewModel;

class BlogController extends AbstractActionController
{
	public function indexAction()
	{
        /**
         * @var News $tableNews
         */
		$tableNews = $this->getServiceLocator()->get('Pages\Tables\News');

        /**
         * @var array $news
         */
		$news = [];

        /**
         * @var Where $predicate
         */
		$predicate = new Where();

		$predicate->equalTo('locale', $this->params('locale'));

		if ($this->params('category')) {
			$category = $this->getServiceLocator()
                ->get(Categories::class)
                ->getWith(['url' => $this->params('category')])
                ->current();
		
			if ($category) {
				$predicate->equalTo('category', $category->id);
			} else {
				$this->getResponse()->setStatusCode(404);
				return;
			}
		}

		foreach ($tableNews->fetchAll($predicate) as $item) {
			$news[] = $item;
		}

        /**
         * @var Paginator $paginator
         */
		$paginator = new Paginator(new ArrayPaginator($news));

		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage((int)$this->params()->fromQuery('items', 10));

		return new ViewModel([
            'posts' => $paginator,
            'news' => $news,
            'category' => isset($category) ? $category : null,
        ]);
	}
}
?>