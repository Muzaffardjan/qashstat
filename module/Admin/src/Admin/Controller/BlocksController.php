<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class BlocksController extends AbstractActionController
{
	public function indexAction()
	{
		$this->layout('layout/admin');
		$config = $this->getServiceLocator()->get('config');

		return array(
			'blocks' 	=> $config['blocks'],
			'locales' 	=> $config['translator']['locales'],
		);
	}

	public function editAction()
	{
		$form 		= new \Admin\Form\Blocks();
		$config 	= $this->getServiceLocator()->get('config');
		$blocks 	= $config['blocks'];
		$locales 	= $config['translator']['locales'];

		if(!isset($blocks[$this->params('block')]) || !isset($locales[$this->params('target')]))
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$this->layout('layout/admin');
		$blocksConfig 	= $blocks;
		$blocks 		= $this->getServiceLocator()->get('Blocks');
		$block 			= $blocks->getBlock($this->params('block'), $this->params('target'));

		if($this->getRequest()->isPost())
		{
			$form->setData($this->getRequest()->getPost());

			if($form->isValid())
			{
				$formData = $form->getData();
				$blocks->setBlock($this->params('block'), $this->params('target'), $formData['content']);

				return $this->redirect()->toRoute('admin/blocks', array('locale' => $this->params('locale')));
			}
		}
		else
		{
			$form->get('content')->setValue(is_object($block)? $block->content: '');
		}	

		return array(
			'block' => $block,
			'config'=> $blocksConfig[$this->params('block')],
			'form'	=> $form,
			'target'=> $this->params('target'),
			'name'	=> $this->params('block'),
		);
	}
}
?>