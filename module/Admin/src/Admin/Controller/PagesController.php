<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class PagesController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout('layout/admin');
        $tablePages = $this->getServiceLocator()->get('Pages\Tables\Pages');
        $config     = $this->getServiceLocator()->get('config');
        $locales    = $config['translator']['locales'];
        $pages      = array();
        $alternatives = array();

        foreach($tablePages->fetchAll() as $page)
        {
            $pages[] = $page;

            if($page->getChainId())
            {
                $alternatives[$page->getChainId()][$page->locale] = $page;
            }
        }

        foreach($pages as $page)
        {
            if($page->getChainId() && isset($alternatives[$page->getChainId()]))
            {
                $page->setAlternatives($alternatives[$page->getChainId()]);
            }
        }

        return array(
            'locales'   => $locales,
            'pages'     => $pages,
        );
    }

    public function addAction()
    {
        $this->layout('layout/admin');

        $config     = $this->getServiceLocator()->get('config');
        $form       = new \Admin\Form\Pages();
        $request    = $this->getRequest();
        $locales    = $config['translator']['locales'];
        $tablePages = $this->getServiceLocator()->get('Pages\Tables\Pages');

        $form->get('locale')->setValueOptions($locales);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $page       = new \Pages\ArrayObject\Page();
                $formData   = $form->getData();

                $page->setData($formData);

                $page->url = \Application\Library\Stdlib::get_url_string(
                    $page->title
                );


                $tablePages->add($page);

                $page->id     = $tablePages->getTablegateway()->lastInsertValue;

                if ((bool) $page->visible) {
                    $this->getServiceLocator()
                        ->get('Indexing\ZendSearch')
                        ->add($page);
                }

                return $this->redirect()->toRoute(
                    'admin/pages',
                    ['locale' => $this->params('locale')]
                );
            }
        }

        return [
            'form'    => $form,
            'locales' => $locales,
        ];
    }

    public function editAction()
    {
        $this->layout('layout/admin');

        $config     = $this->getServiceLocator()->get('config');
        $request    = $this->getRequest();
        $tablePages = $this->getServiceLocator()->get('Pages\Tables\Pages');

        if (!$page = $tablePages->getByUrl($this->params('page'))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form    = new \Admin\Form\Pages();
        $locales = $config['translator']['locales'];
        $errors  = [];

        $form->get('locale')->setValueOptions($locales);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $formData  = $form->getData();
                $oldLocale = $page->locale;

                $page->exchangeArray($formData);

                $indexer = $this->getServiceLocator()->get('Indexing\ZendSearch');
                
                foreach ($indexer->find('title:"'.str_replace('"','\"',$page->title).'"') as $hit) {
                    if ($hit->page == $page->id) {
                        $indexer->delete($hit->id);
                    }
                }           

                if ((bool) $page->visible) {
                    $indexer->add($page);
                }

                $tablePages->edit($page);

                return $this->redirect()->toRoute('admin/pages', array('locale' => $this->params('locale')));
            }
        }

        $form->setData($page->getData());

        return array(
            'form'      => $form,
            'locales'   => $locales,
            'page'      => $page,
            'errors'    => $errors,
        );  
    }

    public function deleteAction()
    {
        $this->layout('layout/admin');

        $request    = $this->getRequest();
        $tablePages = $this->getServiceLocator()->get('Pages\Tables\Pages');
        
        if (!$page = $tablePages->getByUrl($this->params('page'))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($request->getQuery('confirm')) {
            $tablePages->delete($page->id);

            $indexer = $this->getServiceLocator()->get('Indexing\ZendSearch');

            foreach (
                $indexer->find('title:"'.str_replace('"','\"',$page->title).'"') 
                as $hit) {
                if ($hit->page == $page->id) {
                    $indexer->delete($hit->id);
                }
            }

            return $this->redirect()->toRoute(
                'admin/pages',
                ['locale' => $this->params('locale')]
            );
        }

        return [
            'page' => $page,
        ];
    }
}
?>