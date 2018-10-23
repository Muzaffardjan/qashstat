<?php
namespace Quiz\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    public function voteAction()
    {
    	$request 	= $this->getRequest();
    	$quiz 		= $this->getServiceLocator()->get('Quiz\Quiz');
    	$provider 	= $quiz->getQuestionsProvider();

    	if($request->isPost() && $question = $provider->getQuestion($this->params('id')))
    	{
	    	$form 	= new \Quiz\Form\Vote();

	    	$form->addOptions($question->getAnswers());
    		$form->setData($request->getPost());

    		if($form->isValid())
    		{
    			$formData 	= $form->getData();
    			$stats 		= array();

    			// Vote for answer
    			$quiz->vote($formData['options']);
    			// Reload question
    			$question = $provider->getQuestion($this->params('id'));

    			foreach($question->getAnswers() as $answer)
	    		{
	    			$stats[] = array(
	    				'order' => $answer->getOrder(),
	    				'votes'	=> $answer->voted,
	    				'text'	=> $answer->text,
	    			);
	    		}

    			return new JsonModel(array(
    				'question' 	=> array(
    					'votes' => $question->getVotesCount(),
    				),
    				'answers' 	=> $stats,
    			));
    		}
    	}

    	$this->getResponse()->setStatusCode(404);
        return;
    }
}
