<?php
namespace Feedback\View\Helper;

use Zend\View\Helper\AbstractHelper;

class FeedbackHelper extends AbstractHelper
{
	protected $provider;

	protected $count;

	protected $unreaded;

	public function __invoke()
	{
		return $this;
	}

	public function count()
	{
		if(null === $this->count)
		{	
			$this->count = $this->getProvider()->getCount();
		}

		return (int) $this->count;
	}

	public function fetch()
	{
		if(null === $this->unreaded)
		{
			$this->unreaded = array();

			// Assign all new unreaded feedbacks to unreaded property
			foreach($this->getProvider()->getNewFeedback() as $feedback)
			{
				$this->unreaded[] = $feedback;
			}
		}

		return $this->unreaded;
	}

	protected function getProvider()
	{
		if(null === $this->provider)
		{
			$this->provider = $this->getView()->getHelperPluginManager()->getServiceLocator()->get('Feedback\Tables\Feedback'); 
		}
		
		return $this->provider;
	}
}