<?php
namespace Application;

use Zend\View\Model\ViewModel;

class CalendarController extends AbstractActionController
{
	public function indexAction()
	{
		return new ViewModel([]);
	}
}