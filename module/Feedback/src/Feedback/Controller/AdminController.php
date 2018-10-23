<?php 

namespace Feedback\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
	public function indexAction()
	{
		$this->layout('layout/admin');
		$feedbackTable = $this->getServiceLocator()->get('Feedback\Tables\Feedback');

		$feedbacks = array();

		foreach($feedbackTable->fetchAll() as $feedback)
		{
			$feedbacks[] = $feedback;
		}

		return array(
			'feedbacks' => $feedbacks
		);
	}

	public function readAction()
	{
		$this->layout('layout/admin');
		$id = $this->params()->fromRoute('id');
		$id = (int)$id;
		$feedbackTable = $this->getServiceLocator()->get('Feedback\Tables\Feedback');
		$feedbackTable->updateFeedbackStatus($id);
		$feedback = $feedbackTable->getFeedback($id);
		$answerForm = new \Feedback\Form\Answer();
		return array(
			'feedback' => $feedback,
			'form'		=> $answerForm,
		);
	}

	public function replyAction()
	{
		$this->layout('layout/admin');
		$form = new \Feedback\Form\Answer();
		$id = (int)$this->params()->fromRoute('id');
		$table = $this->getServiceLocator()->get('Feedback\Tables\Feedback');
		$feedback = $table->getFeedback($id);
		$to = $feedback->email;

        if($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if($form->isValid())
            {
            	$data = $form->getData();

            	$sendData = array(
					'to' 		=> $to,
					'body' 		=> $data['text'],
					'subject' 	=> $data['subject'],
				);

				$sender = $this->getServiceLocator()->get('Feedback\Sender\Sender');

				$sender->send($sendData);

                return array(
                    'success' 		=> true,
                    'form'	  		=> $form,
                    'feedback'		=> $feedback,
                );      
            }
        }	
		return array(
			'form' 			=> $form,
			'feedback'		=> $feedback,
		);
	}

	public function deleteAction()
	{
		$this->layout('layout/admin');
		$request 	= $this->getRequest();
		$id 		= (int)$this->params()->fromRoute('id');
		$table 		= $this->getServiceLocator()->get('Feedback\Tables\Feedback');
		if ($request->getQuery('confirm'))
		{
			$result = $table->delete($id);
			return $this->redirect()->toRoute('admin/feedback', array('locale' => $this->params('locale')));
		}
		$feedback 	= $table->getFeedback($id);
		return array(
			'feedback' => $feedback,
		);
	}
}