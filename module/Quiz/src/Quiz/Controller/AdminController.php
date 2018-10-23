<?php
namespace Quiz\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Mvc\MvcEvent;
use Quiz\Form\Quiz as QuizForm;
use Quiz\Form\Answers as AnswersForm;
use Quiz\Questions\Question;
use Quiz\Questions\Answer;

class AdminController extends AbstractActionController
{
	public function onDispatch(MvcEvent $e)
	{
		$this->layout('layout/admin');

		parent::onDispatch($e);
	}

	public function indexAction()
	{
		$provider 	= $this->getServiceLocator()->get('Quiz\Quiz')->getQuestionsProvider();
		$questions 	= array();
		$locales 	= $this->getServiceLocator()->get('config')['translator']['locales'];

		foreach($provider->getQuestions() as $question)
		{
			$questions[] = $question;
		}

		return array(
			'questions' => $questions,
			'locales'	=> $locales,
		);
	}

	public function addAction()
	{
		$form 		= new QuizForm();
		$config 	= $this->getServiceLocator()->get('config');
		$locales	= $config['translator']['locales'];
		$request 	= $this->getRequest();

		// Set locales to locale select
		$form->get('locale')->setValueOptions($locales);

		if($request->isPost())
		{
			$form->setData($request->getPost());

			if($form->isValid())
			{
				$question = new Question();

				// Set question data
				$question->setData($form->getData());

				$provider = $this->getServiceLocator()->get('Quiz\Quiz')->getQuestionsProvider();
				
				// Save question
				$provider->addQuestion($question);

				$this->flashMessenger()->addMessage('New question was added successfully', FlashMessenger::NAMESPACE_SUCCESS);

				return $this->redirect()->toRoute(
					'admin/quiz',
					array(
						'locale' => $this->params('locale'),
						'action' => 'edit',
						'id'	 => $provider->getQuestionsTable()->getTableGateway()->lastInsertValue,
					)
				);
			}
		}

		return array(
			'form' => $form,
		);
	}

	public function editAction()
	{
		$quiz 		= $this->getServiceLocator()->get('Quiz\Quiz');
		$provider 	= $quiz->getQuestionsProvider();

		if($question = $provider->getQuestion($this->params('id')))
		{
			$form 		= new QuizForm();
			$config 	= $this->getServiceLocator()->get('config');
			$locales	= $config['translator']['locales'];
			$request 	= $this->getRequest();

			// Set locales to locale select
			$form->get('locale')->setValueOptions($locales);

			if($request->isPost())
			{
				$form->setData($request->getPost());

				if($form->isValid())
				{
					// Exchange so that keep unchanged values
					$question->exchangeArray($form->getData());

					$provider->updateQuestion($question);

					// Flash success message
					$this->flashMessenger()
					->addMessage
					(
						'Question was updated successfully', 
						FlashMessenger::NAMESPACE_SUCCESS
					);

					return $this->redirect()->toRoute(
						'admin/quiz',
						array(
							'locale' => $this->params('locale'),
						)
					);
				}
			}
			else
			{
				$form->setData($question->getData());
			}

			return array(
				'form' 		=> $form,
				'question' 	=> $question,
			);
		}

		// Question doesn't exists
		$this->getResponse()->setStatusCode(404);
		return;
	}

	public function deleteAction()
	{
		$quiz 		= $this->getServiceLocator()->get('Quiz\Quiz');
		$provider 	= $quiz->getQuestionsProvider();

		if($question = $provider->getQuestion($this->params('id')))
		{
			$request 	= $this->getRequest();

			if($request->getQuery('confirm'))
			{
				// Delete question
				$this->getServiceLocator()->get('Quiz\Quiz')
				->getQuestionsProvider()
				->deleteQuestion($question);

				// Flash message succes operation
				$this->flashMessenger()
				->addMessage
				(
					'Question was deleted', 
					FlashMessenger::NAMESPACE_SUCCESS
				);

				return $this->redirect()->toRoute(
					'admin/quiz',
					array(
						'locale' => $this->params('locale')
					)
				);
			}

			return array(
				'question' => $question,
			);
		}

		// Question doesn't exists
		$this->getResponse()->setStatusCode(404);
		return;
	}

	public function addAnswerAction()
	{
		$quiz 		= $this->getServiceLocator()->get('Quiz\Quiz');
		$provider 	= $quiz->getQuestionsProvider();

		if($question = $provider->getQuestion($this->params('id')))
		{
			$form 		= new AnswersForm();
			$request 	= $this->getRequest();
			$orders		= array();

			for($i=1; $i<=$question->countAnswers(); $i++)
			{
				$orders[$i] = $i;
			}

			$form->get('index')->setValueOptions($orders);

			if($request->isPost())
			{
				$form->setData($request->getPost());

				if($form->isValid())
				{
					$answer  = new Answer();
					$formData= $form->getData();

					// Form data to answer
					$answer->setData($formData);
					
					$answer->question 	= $question->id;
					$answer->voted 		= 0;

					if(!isset($formData['index']) || $formData['index'] == '' || is_null($formData['index']) || $formData['index'] === false)
					{
						// By default answer order is last
						$answer->index = $question->countAnswers();
					}
					elseif(((int) $formData['index']) > 0)
					{
						// Order starts with 0 so that substract 1 from ui select
						$answer->index = $formData['index'] - 1;

						// Change order of conflicting answer if exists
						$provider->getAnswersTable()
						->setOrder
						(
							// Change answer with belo order
							$answer->index, 
							// To below
							$question->countAnswers(), 
							// Just question id for predicate
							$question->id
						);
					}

					// Add answer
					$provider->addAnswer($answer, $question);

					// Flash message succes operation
					$this->flashMessenger()
					->addMessage
					(
						'New answer was added successfully', 
						FlashMessenger::NAMESPACE_SUCCESS
					);

					return $this->redirect()->toRoute
					(
						'admin/quiz',
						array(
							'locale' => $this->params('locale'),
							'action' => 'edit',
							'id'	 => $question->id,
						)
					);
				}
			}

			return array(
				'form' 		=> $form,
				'question' 	=> $question,
			);
		}

		// Question doesn't exists
		$this->getResponse()->setStatusCode(404);
		return;
	}

	public function editAnswerAction()
	{
		$quiz 		= $this->getServiceLocator()->get('Quiz\Quiz');
		$provider 	= $quiz->getQuestionsProvider();

		if($answer = $provider->getAnswersTable()->getBy(array('id' => $this->params('id')))->current())
		{
			$form 		= new AnswersForm();
			$question 	= $provider->getQuestion($answer->question);
			$request 	= $this->getRequest();
			$orders		= array();

			for($i=1; $i<=$question->countAnswers(); $i++)
			{
				$orders[$i] = $i;
			}

			$form->get('index')->setValueOptions($orders);

			if($request->isPost())
			{
				$form->setData($request->getPost());

				if($form->isValid())
				{
					$formData = $form->getData();

					if(isset($formData['index']) && !is_null($formData['index']) && $formData['index'] != '')
					{
						// Order starts from 0
						$formData['index'] -= 1;

						if($formData['index'] != $answer->getOrder())
						{
							// Change order of conflicting answer if exists
							$provider->getAnswersTable()
							->setOrder
							(
								// Change answer with below order
								$formData['index'], 
								// To below
								$answer->getOrder(), 
								// Just question id for predicate
								$answer->question
							);
						}
					}
					else
					{
						$formData['index'] = $question->countAnswers();
					}

					$answer->exchangeArray($formData);
					
					// be sure that answer not changed question
					$answer->question 	= $question->id;
					
					// Little illigal operation ;)
					if($request->getQuery('vote_count'))
					{
						$answer->voted = (int) $request->getQuery('vote_count');
					}

					// Change answer
					$provider->updateAnswer($answer);

					// Flash message succes operation
					$this->flashMessenger()
					->addMessage
					(
						'Answer was successfully changed', 
						FlashMessenger::NAMESPACE_SUCCESS
					);

					return $this->redirect()->toRoute
					(
						'admin/quiz',
						array(
							'locale' => $this->params('locale'),
							'action' => 'edit',
							'id'	 => $answer->question,
						)
					);
				}
			}
			else
			{
				$form->setData($answer->getData());
				$form->get('index')->setValue($answer->getOrder() + 1);
			}

			return array(
				'form' 		=> $form,
				'answer' 	=> $answer,
			);
		}

		// Answer doesn't exists
		$this->getResponse()->setStatusCode(404);
		return;
	}

	public function deleteAnswerAction()
	{
		$quiz 		= $this->getServiceLocator()->get('Quiz\Quiz');
		$provider 	= $quiz->getQuestionsProvider();

		if($answer = $provider->getAnswersTable()->getBy(array('id' => $this->params('id')))->current())
		{
			$request 	= $this->getRequest();

			if($request->getQuery('confirm'))
			{
				// Delete question
				$this->getServiceLocator()->get('Quiz\Quiz')
				->getQuestionsProvider()
				->deleteAnswer($answer);

				// Flash message success operation
				$this->flashMessenger()
				->addMessage
				(
					'Answer was deleted', 
					FlashMessenger::NAMESPACE_SUCCESS
				);

				return $this->redirect()->toRoute(
					'admin/quiz',
					array(
						'locale' => $this->params('locale'),
						'action' => 'edit',
						'id'	 => $answer->question,
					)
				);
			}

			return array(
				'answer' => $answer,
			);
		}

		// Answer doesn't exists
		$this->getResponse()->setStatusCode(404);
		return;
	}

	public function statsAllAction()
	{
		$quiz 		= $this->getServiceLocator()->get('Quiz\Quiz');
		$provider 	= $quiz->getQuestionsProvider();
		$questions 	= array();
		$resultset  = $provider->getByVoteCount();
		//$resultset  = $provider->getQuestions();

		foreach($resultset as $question)
		{
			$questions[] = $question;
		}

		return array(
			'questions' => $questions,
		);
	}
}