<?php  
namespace Pages\View\Helper\Plugins;

use Pages\View\Helper\PagesHelperPluginInterface;
use Pages\ArrayObject\News as NewsArrayObject;
use Zend\View\Renderer\PhpRenderer;
use Zend\ServiceManager\ServiceManager;

class News implements PagesHelperPluginInterface
{
    protected $serviceLocator;

    protected $view;

    protected $partial;

    protected $table;

    protected $currentSet;

    public function setView(PhpRenderer $view)
    {
        $this->view = $view;

        return $this;
    }

    public function setServiceLocator(ServiceManager $sm)
    {
        $this->serviceLocator = $sm;

        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setTable(\Pages\Tables\News $table)
    {
        $this->table = $table;
    }

    public function getTable()
    {
        if(null === $this->table) {
            $this->table = $this->getServiceLocator()->get('Pages\Tables\News');
        }

        return $this->table;
    }

    public function setPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    public function getPartial()
    {
        return $this->partial;
    }

    public function fetchAll($category = null, $order = 'id DESC', $limit = null)
    {
        $table = $this->getTable();
        $where = ['locale' => $this->view->currentLocale()];

        if(null !== $category) {
            $where['category'] = $category;
        }

        $resultSet = $table->fetchAll($where, $order, $limit);
        $result = array();

        foreach($resultSet as $item) {
            $result[] = $item;
        }

        $this->currentSet = $result;

        return $this;
    }

    public function latest($category = null, $limit = 5)
    {
        $table = $this->getTable();
        $where = ['locale' => $this->view->currentLocale()];

        if(null !== $category) {
            $where['category'] = $category;
        }

        $resultSet = $table->fetchAll($where, 'added DESC', $limit);
        $result = array();

        foreach($resultSet as $item) {
            $result[] = $item;
        }

        $this->currentSet = $result;

        return $this;
    }

    public function related(NewsArrayObject $news, $limit = 5)
    {
        $table      = $this->getTable();
        $indexer    = $this->getServiceLocator()->get('Indexing\ZendSearch');

        $hits   = $indexer->find($news->title . ' ' . $news->description);
        $ids    = array();
        $result = array();

        foreach($hits as $hit) {
            if(
                $hit->locale == $news->locale && 
                $news->id != $hit->page && 
                count($result) < $ids
            ) {
                $ids[] = $hit->page;
            }
        }

        if(count($ids)) {
            $resultSet = $table->getWith(array('id' => $ids));

            foreach($resultSet as $item) {
                $result[] = $item;
            }

            $this->currentSet = $result;
        }

        return $this;
    }

    public function render()
    {
        if(null !== ($partial = $this->getPartial())) {
            return $this->view->partial(
                $partial, 
                ['container' => $this->currentSet]
            );
        }

        $result = '<ul>';

        foreach($this->currentSet as $item) {
            $result .= '<li>'.$item->title.'</li>';
        }

        $result .= '</ul>';

        return $result;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function __invoke()
    {
        return $this;
    }

    public function getCurrentSet()
    {
        return $this->currentSet;
    }
}
?>