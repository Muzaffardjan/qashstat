<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    	$this->layout('layout/admin');

        new \Quiz\Questions\Tables\Answers();
    	
        return array();
    }
}
