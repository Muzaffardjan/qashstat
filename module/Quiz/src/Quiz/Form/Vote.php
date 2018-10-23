<?php  
namespace Quiz\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Quiz\Questions\AbstractAnswer;

class Vote extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'quiz-vote-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' 	=> 'options',
			'type'	=> 'Radio',
			'options' => array(
			),
		));
	}

	public function addOptions($answers)
	{
		$options = array();

		foreach($answers as $answer)
		{
			$options[$answer->id] = $answer->text;
		}

		$this->get('options')->setValueOptions($options);
	}

	public function getInputFilterSpecification()
	{
		return array(
			
		);
	}
}
?>