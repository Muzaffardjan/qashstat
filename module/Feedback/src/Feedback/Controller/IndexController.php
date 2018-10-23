<?php

namespace Feedback\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Feedback\Form\Feedback;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $form = new Feedback();

        if($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if($form->isValid())
            {
                $this->create($form->getData());

                return array(
                    'success' => true,
                );      
            }
        }

        return array(
            'form'      => $form,
            'success'   => null,
        );
    }

    public function processAction()
    {
        if(!$this->request->isPost())
        {
            return $this->redirect->toRoute('home');
        }

        $post = $this->request->getPost();

        $form = new Feedback();
        $form->setData($post);
        if(!$form->isValid())
        {
            $error = array(
                'error' => true,
                'form'  => $form,
            );

            //$error->setTemplate('feedback\feedback\index');
            return $error;
        }
        else
        {
            $formData = $form->getData();
            $this->create($formData);
            return $this->redirect()->toRoute('feedback/default', array(
                'controller' => 'index',
                'action'     => 'confirm',
            ));
        }
        
    }

    public function confirmAction()
    {
        return array();
    }

    public function create($data)
    {
        $arrayObject = new \Feedback\ArrayObject\Feedback();
        $arrayObject->exchangeArray($data);
        $feedbackTable = $this->getServiceLocator()->get('Feedback\Tables\Feedback');
        $feedbackTable->saveFeedback($arrayObject);
        return true;
    }
}