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

use Admin\Form\Categories;
use Application\Library\Stdlib;
use Pages\ArrayObject\Category;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class CategoriesController extends AbstractActionController
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
         * @var \Pages\Tables\Categories $categoryTable
         */
        $categoryTable = $this->getServiceLocator()->get('Pages\Tables\Categories');

        /**
         * @var array $categories
         */
        $categories = [];

        foreach ($categoryTable->fetchAll() as $category) {
            $categories[] = $category;
        }

        return new ViewModel([
            'categories' => $categories,
        ]);
    }

    public function addAction()
    {
        /**
         * @var Categories $form
         */
        $form = new Categories();

        /**
         * @var Request $request
         */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                /**
                 * @var Category $category
                 */
                $category = new Category();

                /**
                 * @var \Pages\Tables\Categories $categoryTable
                 */
                $categoryTable = $this->getServiceLocator()->get('Pages\Tables\Categories');

                $category->setData([
                    'title' => $data['title'],
                    'url' => Stdlib::get_url_string($data['title']),
                    'locale' => 'uz-UZ',
                ]);

                $categoryTable->add($category);

                return $this->redirect()->toRoute(
                    'admin/categories',
                    ['locale' => $this->params('locale')]
                );
            }
        }

        return new ViewModel([
            'form' => $form
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
         * @var \Pages\Tables\Categories $categoryTable
         */
        $categoryTable = $this->getServiceLocator()->get('Pages\Tables\Categories');

        if (! $category = $categoryTable->getCategoryById($this->params('category'))) {
            $response->setStatusCode(404);
            return;
        }

        if ($request->getQuery('confirm')) {
            $categoryTable->delete($category->id);

            return $this->redirect()->toRoute(
                'admin/categories',
                ['locale' => $this->params('locale')]
            );
        }

        return new ViewModel([
            'category' => $category,
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
         * @var \Pages\Tables\Categories $categoryTable
         */
        $categoryTable = $this->getServiceLocator()->get('Pages\Tables\Categories');

        /**
         * @var Categories $form
         */
        $form = new Categories();

        if (! $category = $categoryTable->getCategoryById($this->params('category'))) {
            $response->setStatusCode(404);
            return;
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $category->exchangeArray($form->getData());

                $categoryTable->edit($category);

                return $this->redirect()->toRoute(
                    'admin/categories',
                    ['locale' => $this->params('locale')]
                );
            }
        } else {
            $form->setData($category->getData());
        }

        return new ViewModel([
            'form' => $form,
            'category' => $category,
        ]);
    }
}

