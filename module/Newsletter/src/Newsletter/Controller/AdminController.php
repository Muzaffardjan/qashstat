<?php
/**
 * Newsletter module admin controller
 *
 * @link http://each.uz/
 * @copyright  Copyright (c) 2016. Foreach Soft Ltd.
 * 
 */

namespace Newsletter\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AdminController extends AbstractActionController
{
    public function indexAction()
    {
    	$this->layout('layout/admin');
    	$table = $this->getServiceLocator()->get('Newsletter\Tables\Newsletter');
    	$subscribers = array();

    	foreach($table->fetchAll() as $subscriber)
		{
			$subscribers[] = $subscriber;
		}

        return array('subscribers' => $subscribers);
    }

    public function deleteAction()
    {
    	$table = $this->getServiceLocator()->get('Newsletter\Tables\Newsletter');
    	$link = $this->params()->fromRoute('link');
    	$table->unSubscribe($link);
    	return $this->redirect()->toRoute('admin/newsletter', array('locale' => $this->params('locale')));
    }
}
