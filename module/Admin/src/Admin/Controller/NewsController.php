<?php
/**
 * Qashqadaryo viloyat statistika boshqarmasi.
 *
 * @author    Muzaffardjan Karaev
 * @copyright Copyright (c) "K-SOFT" LTD 2017-2018
 * @license   "K-SOFT" LTD LICENSE
 * @link      https://karaev.uz
 * Created:   06.01.2018
 */

namespace Admin\Controller;

use Pages\ArrayObject\Category;
use Pages\Tables\Categories;
use Pages\Tables\News;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class NewsController extends AbstractActionController
{
    /**
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->layout('layout/admin');

        return parent::onDispatch($e);
    }

    public function indexAction()
    {
        /**
         * @var News $newsTable
         */
        $newsTable = $this->getServiceLocator()->get('Pages\Tables\News');

        /**
         * @var Categories $categoryTable
         */
        $categoryTable = $this->getServiceLocator()->get('Pages\Tables\Categories');

        $news = [];
        $cats = [];

        /**
         * @var Category $value
         */
        foreach ($categoryTable->fetchAll() as $value) {
            $cats[$value->id] = $value;
        }

        /**
         * @var \Pages\ArrayObject\News $item
         */
        foreach ($newsTable->fetchAll() as $item) {
            $news[] = $item;
        }

        return new ViewModel([
            'news' => $news,
            'categories' => $cats
        ]);
    }

    public function addAction()
    {
        /**
         * @var Request $request
         */
        $request = $this->getRequest();

        /**
         * @var News $form
         */
        $form = new \Admin\Form\News();

        /**
         * @var News $newsTable
         */
        $newsTable = $this->getServiceLocator()->get('Pages\Tables\News');

        /**
         * @var Categories $categoryTable
         */
        $categoryTable = $this->getServiceLocator()->get('Pages\Tables\Categories');

        $categories = [];

        /**
         * @var Category $category
         */
        foreach ($categoryTable->fetchAll() as $category) {
            $categories[$category->id] = $category->title;
        }

        $form->get('category')->setValueOptions($categories);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /**
                 * @var \Pages\ArrayObject\News $news
                 */
                $news = new \Pages\ArrayObject\News();

                $data = $form->getData();

                /**
                 * @var \DateTime $datetime
                 */
                $datetime = \DateTime::createFromFormat('d-m-Y', $data['added']);

                $news->setData([
                    'locale' => 'uz-UZ',
                    'category' => $data['category'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'body' => $data['body'],
                    'image' => $data['image'],
                    'added' => $datetime->getTimestamp(),
                ]);

                $newsTable->add($news);

                /*$news->id = $this->getTablegateway()->lastInsertValue;
                $this->getServiceLocator()
                ->get('Indexing\ZendSearch')
                ->add($news);*/

                return $this->redirect()->toRoute(
                    'admin/news',
                    ['locale' => $this->params('locale')]
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'categories' => $categories,
        ]);
    }

    public function deleteAction()
    {
        /**
         * @var Request $request
         */
        $request = $this->getRequest();

        /**
         * @var Response $response
         */
        $response = $this->getResponse();

        /**
         * @var News $newsTable
         */
        $newsTable = $this->getServiceLocator()->get('Pages\Tables\News');

        if (! $news = $newsTable->getNewsById($this->params('id'))) {
            $response->setStatusCode(404);
            return;
        }

        if ($request->getQuery('confirm')) {
            $newsTable->delete($news->id);

            return $this->redirect()->toRoute(
                'admin/news',
                ['locale' => $this->params('locale')]
            );
        }

        return new ViewModel([
            'news' => $news,
        ]);
    }

    public function editAction()
    {
        /**
         * @var Request $request
         */
        $request = $this->getRequest();

        /**
         * @var Response $response
         */
        $response = $this->getResponse();

        /**
         * @var \Admin\Form\News $form
         */
        $form = new \Admin\Form\News();

        /**
         * @var News $newsTable
         */
        $newsTable = $this->getServiceLocator()->get('Pages\Tables\News');

        /**
         * @var Categories $categoryTable
         */
        $categoryTable = $this->getServiceLocator()->get('Pages\Tables\Categories');

        /**
         * @var \Pages\ArrayObject\News $news
         */
        if (! $news = $newsTable->getNewsById($this->params('id'))) {
            $response->setStatusCode(404);
            return;
        }

        /**
         * @var array $categories
         */
        $categories = [];

        /**
         * @var Category $category
         */
        foreach ($categoryTable->fetchAll() as $category) {
            $categories[$category->id] = $category->title;
        }

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                $data = $form->getData();

                /**
                 * @var \DateTime $datetime
                 */
                $datetime = \DateTime::createFromFormat('d-m-Y', $data['added']);

                $news->exchangeArray([
                    'category' => $data['category'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'body' => $data['body'],
                    'image' => $data['image'],
                    'added' => $datetime->getTimestamp(),
                ]);

                $newsTable->edit($news);

                return $this->redirect()->toRoute(
                    'admin/news',
                    ['locale' => $this->params('locale')]
                );
            }
        } else {
            $form->get('category')->setValueOptions($categories);
            $form->setData($news->getData());
            $form->get('added')->setAttribute('value', date('d-m-Y', $news->added));
        }

        return new ViewModel([
            'categories' => $categories,
            'form' => $form,
            'news' => $news,
        ]);
    }
}

