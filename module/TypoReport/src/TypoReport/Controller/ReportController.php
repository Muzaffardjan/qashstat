<?php  
namespace TypoReport\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use TypoReport\Form\Report;

class ReportController extends AbstractActionController
{
	public function indexAction()
	{
		$form 		= new Report();
		$request 	= $this->getRequest();

		if($request->isPost())
		{
			$form->setData($request->getPost());
			$translator = $this->getServiceLocator()->get('translator');

			if($form->isValid())
			{
				$formData 	= $form->getData();
				$typo 		= new \TypoReport\ArrayObject\Typo();

				$typo->setData($formData);

				$typo->reported_in = time();

				$this->getServiceLocator()
				->get('TypoReport\TypoReport')
				->report($typo);

				return new JsonModel(array(
					'status' 	=> true,
					'messages' 	=> [$translator->translate('Typo was reported. Thank you for support.')],
				));
			}
			else
			{
				$messages = array();

				foreach($form->getMessages() as $elementMessages)
				{
					if(is_array($elementMessages))
					{
						foreach($elementMessages as $message)
						{
							$messages[] = $translator->translate($message);
						}
					}
				}

				return new JsonModel(array(
					'status' 	=> false,
					'messages' 	=> $messages,
				));
			}
		}

		$this->getResponse()->setStatusCode(404);
		return;
	}
}
?>