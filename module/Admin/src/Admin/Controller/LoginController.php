<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LoginController extends AbstractActionController
{
	public function indexAction()
	{
		if($this->identity())
		{
			var_dump($this->identity());
			return $this->redirect()->toRoute('admin/default', array('locale' => $this->params('locale')));
		}

		$form = new \Admin\Form\LoginUser();
		$request = $this->getRequest();
		$errors = array();

		if($request->isPost())
		{
			$form->setData($request->getPost());

			if($form->isValid())
			{
				$service 	= $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
				$formData 	= $form->getData();
				$control 	= new \Users\ArrayObject\Control();

				$control->setData($formData);
				$control->hashPassword();

				$service->getAdapter()
				->setUsername($control->login)
				->setPassword($control->password);

				$result = $service->authenticate();

				if($result->isValid())
				{
					$service->getStorage()->write($result->getIdentity());

					return $this->redirect()->toRoute('admin/default', array('locale' => $this->params('locale')));
				}
				else
				{
					$errors[] = 'Wrong login or password';
					$errors[] = 'Tip: credentials are case sensetive';
				}
			}
		}
		$this->layout('layout/login');
		return array(
			'form' => $form,
			'errors' => $errors
		);
	}
}
?>