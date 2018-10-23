<?php  
namespace TypoReport\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Mvc\MvcEvent;

class AdminController extends AbstractActionController
{
	public function onDispatch(MvcEvent $e)
	{
		$this->layout('layout/admin');

		parent::onDispatch($e);
	}

	public function indexAction()
	{
		$typoReport = $this->getServiceLocator()->get('TypoReport\TypoReport');
		$reports 	= array();

		foreach($typoReport->fetchAll() as $report)
		{
			$reports[] = $report; 
		}

		return array(
			'reports' => $reports,
		);
	}

	public function correctAction()
	{
		$typoReport = $this->getServiceLocator()->get('TypoReport\TypoReport');
		$request 	= $this->getRequest();

		if($this->params('id') && $typo = $typoReport->getById($this->params('id')))
		{
			$typoReport->correct($typo);

			$this->flashMessenger()->addMessage
			(
				'Requested typo marked as corrected!', 
				FlashMessenger::NAMESPACE_SUCCESS
			);

			return $this->redirect()->toRoute('admin/typo-report', array(
				'locale' => $this->params('locale'),
			));
		}

		$this->getResponse()->setStatusCode(404);
		return;
	}
}
?>