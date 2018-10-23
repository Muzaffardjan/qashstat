<?php
/**
 * Newsletter module manage controller
 *
 * @link      http://each.uz/
 * @copyright Copyright (c) 2016 Foreach Soft Ltd
 *
 */

namespace Newsletter\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Newsletter\Tables\Newsletter;
use Newsletter\Form\NewsletterForm;
use Zend\View\Model\ViewModel;

class ManageController extends AbstractActionController
{
    public function indexAction()
    {
        return array();
    }

    public function subscribeAction()
    {
    	if (!$this->request->isPost())
    	{
    		return $this->redirect()->toRoute('home-locale', array('locale' => $this->params('locale')));
    	}
    	$post = $this->request->getPost();
        
            $form = new \Newsletter\Form\NewsletterForm();

            $form->setData($post);
            if (!$form->isValid())
            {
            	return $this->redirect()->toRoute('home');
            }
            try
	            {
	            	$this->writeEmail($form->getData());  
                    $msg = 'You are subscribed succesfully';
	            }
			catch (\Exception $e)
	            {
	            	$msg = 'You are already subscribed';
	            }
	            $this->flashMessenger()->addMessage($msg);
       
       	return $this->redirect()->toRoute('home-locale', array('locale' => $this->params('locale')));

    }

    public function writeEmail($data)
	    {
	        $newsletter = new \Newsletter\ArrayObject\Newsletter();
	        $newsletter->exchangeArray($data);

	        $newsletterTable = $this->getServiceLocator()->get('Newsletter\Tables\Newsletter');
	        $newsletterTable->subscribe($newsletter);

	        return true;
	    }

    public function unSubscribeAction()
    {
    	$link = $this->params()->fromRoute('link');

        $table = $this->getServiceLocator()->get('Newsletter\Tables\Newsletter');
        $table->unSubscribe($link);

        $this->flashMessenger()->addMessage('You are succesfully unsubscribed');

        return $this->redirect()->toRoute('home-locale', array('locale' => $this->params('locale')));
    }

    public function send($e)
    {
        $this->setEvent($this->getServiceLocator()->get('application')->getMvcEvent());

        $news       = $e->getTarget();
        $category   = $this->getServiceLocator()->get('Pages\Tables\Categories')->getWith(array('id' => $news->category))->current();

        if(!$category) {
            return;
        }
        
        $newsUrl = $this->url()
            ->fromRoute(
                'news/view', 
                array(
                    'locale'    => $news->locale, 
                    'category'  => $category->url, 
                    'page'      => $news->url,
                    'year'      => date('Y', $news->added),
                    'month'     => date('m', $news->added),
                    'date'      => date('d', $news->added),
                ),
                array(
                    'force_canonical' => true,
                )
            );
        $blogUrl  = $this->url()
            ->fromRoute(
                'blog/default', 
                array(
                    'locale' => $news->locale
                ),
                array(
                    'force_canonical' => true,
                )
            );

        $sender = $this->getServiceLocator()->get('Newsletter\Sender\Sender');
        $table = $this->getServiceLocator()->get('Newsletter\Tables\Newsletter');

        $email = $table->selectSubscriber();

        $translator = $this->getServiceLocator()->get('Translator');

        $mailBody = $translator->translate('Added new content. For read this please click to link') . $newsUrl;

        foreach ($email as $to) {
            $sendData = array(
                'body'      => $mailBody,
                'to'        => $to->email,
                'subject'   => 'New content',
            );

            $sender->send($sendData);
        }
    }
}
