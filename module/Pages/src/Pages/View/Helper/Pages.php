<?php
namespace Pages\View\Helper;

use Zend\Navigation\AbstractContainer;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Exception;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\RendererInterface as Renderer;

class Pages extends AbstractHelper
{
    protected $categories;

    protected $news;

    protected $pages;

    protected $events;

    /**
    * @var cats
    * @value string
    */
    protected $cats;

    protected function getServiceLocator()
    {
        return $this->getView()->getHelperPluginManager()->getServiceLocator();
    }

    public function __invoke()
    {
        return $this;
    }

    public function getCategories()
    {
        if(null === $this->categories)
        {
            $this->categories = new Plugins\Categories();
            $this->categories->setView($this->getView());
            $this->categories->setServiceLocator($this->getServiceLocator());
        }

        return $this->categories;
    }

    public function getNews($cats = '')
    {
        if(null === $this->news) {
            $this->news = new Plugins\News();
            $this->news->setView($this->getView());
            $this->news->setServiceLocator($this->getServiceLocator());
        }

        return $this->news;
    }

    public function getEvents()
    {
        if(null === $this->events) {
            $this->events = new Plugins\Events();
            $this->events->setView($this->getView());
            $this->events->setServiceLocator($this->getServiceLocator());
        }

        return $this->events;
    }
}
