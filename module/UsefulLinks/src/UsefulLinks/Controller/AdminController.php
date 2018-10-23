<?php  
namespace UsefulLinks\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use UsefulLinks\Form\LinksForm;

class AdminController extends AbstractActionController 
{
	public function onDispatch(MvcEvent $e)
	{
		$this->layout('layout/admin');

		parent::onDispatch($e);
	}

	public function indexAction()
	{
		$table 	= $this->getServiceLocator()->get('UsefulLinks\Table');
		$links 	= array();
		$config = $this->getServiceLocator()->get('config');

		foreach($table->fetchAll('order_number ASC') as $link)
		{
			$links[] = $link;
		}

		return array(
			'links' 	=> $links,
			'locales' 	=> $config['translator']['locales'],
		);
	}

	public function addAction()
	{
		$form 		= new LinksForm(); 
		$request 	= $this->getRequest();
		$config 	= $this->getServiceLocator()->get('config');
		$table 		= $this->getServiceLocator()->get('UsefulLinks\Table');
		$orders		= array();

		for($i=1; $i<=$table->getMaxOrder(); $i++)
		{
			$orders[$i] = $i;
		}

		$form->get('locale')->setValueOptions($config['translator']['locales']);
		$form->get('order')->setValueOptions($orders);

		if($request->isPost())
		{
			$form->setData($request->getPost());

			if($form->isValid())
			{
				$link  		= new \UsefulLinks\Links\Link();

				// Set data from form to link
				$link->setData($form->getData());
				// Get max order
				$maxOrder 	= $table->getMaxOrder($link->locale);

				if($link->order === '')
				{
					$link->order = $maxOrder + 1;
				}
				elseif($maxOrder < $link->order)
				{
					$link->order = $maxOrder + 1;
				}
				else
				{
					// If we're here then link has order conflict
					// Change old link order to last
					$table->setOrderOf($link->order, $maxOrder + 1, $link->locale);
					$link->order = (int) $link->order;
				}

				$table->add($link);
				$this->flashMessenger()->addMessage('New useful link was added', FlashMessenger::NAMESPACE_SUCCESS);

				return $this->redirect()->toRoute
				(
					'admin/useful-links',
					array(
						'locale' => $this->params('locale'),
					)
				);
			}
		}

		return array(
			'form' => $form,
		);
	}

	public function editAction()
	{
		$request 	= $this->getRequest();
		$table 		= $this->getServiceLocator()->get('UsefulLinks\Table');

		if($this->params('id') && $link = $table->getById((int) $this->params('id')))
		{
			$form 		= new LinksForm();
			$config 	= $this->getServiceLocator()->get('config');
			$orders		= array();

			for($i=1; $i<=$table->getMaxOrder(); $i++)
			{
				$orders[$i] = $i;
			}

			$form->get('locale')->setValueOptions($config['translator']['locales']);
			$form->get('order')->setValueOptions($orders);

			if($request->isPost())
			{
				$form->setData($request->getPost());

				if($form->isValid())
				{
					$formData = $form->getData();

					// Get max order
					$maxOrder 	= $table->getMaxOrder($link->locale);

					if($formData['order'] != $link->order)
					{
						if($formData['order'] === '')
						{
							$formData['order'] = $maxOrder + 1;
						}
						elseif($maxOrder < $formData['order'])
						{
							$formData['order'] = $maxOrder + 1;
						}
						else
						{
							// If we're here then link has order conflict
							// Change old link order to last
							$table->setOrderOf($formData['order'], $maxOrder + 1, $formData['locale']);
							$formData['order'] = (int) $formData['order'];
						}
					}

					// Update
					$link->exchangeArray($formData);
					$table->update($link);

					// Flash message
					$this->flashMessenger()->addMessage('Link was updated', FlashMessenger::NAMESPACE_INFO);
					// Redirect
					return $this->redirect()->toRoute
					(
						'admin/useful-links', 
						array(
							'locale' => $this->params('locale')
						)
					);
				}
			}
			else
			{
				$form->setData($link->getData());
			}

			$form->get('locale')->setValueOptions($config['translator']['locales']);
			$form->get('order')->setValueOptions($orders);

			return array(
				'link' => $link,
				'form' => $form,
			);
		}

		$this->getResponse()->setStatusCode(404);
		return;
	}

	public function deleteAction()
	{
		$request 	= $this->getRequest();
		$config 	= $this->getServiceLocator()->get('config');
		$table 		= $this->getServiceLocator()->get('UsefulLinks\Table');

		if($this->params('id') && $link = $table->getById((int) $this->params('id')))
		{
			if($request->getQuery('confirm'))
			{
				// Delete link
				$table->delete((int) $link->id);

				// Flash message
				$this->flashMessenger()->addMessage('Link was deleted', FlashMessenger::NAMESPACE_WARNING);

				return $this->redirect()->toRoute
				(
					'admin/useful-links', 
					array(
						'locale' => $this->params('locale')
					)
				);
			}

			return array(
				'link' => $link,
			);
		}

		$this->getResponse()->setStatusCode(404);
		return;
	}
}
?>