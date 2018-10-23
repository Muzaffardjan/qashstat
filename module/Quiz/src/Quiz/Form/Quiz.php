<?php  
namespace Quiz\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Quiz extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'quiz-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' 			=> 'text',
			'type' 			=> 'Text',
			'attributes' 	=> array(
				'required' 	=> true,
			),
			'options'		=> array(
				'label'		=> 'Question', 
			),
		));

		$this->add(array(
			'name' 			=> 'locale',
			'type' 			=> 'Select',
			'attributes' 	=> array(
				'required' 	=> true,
			),
			'options'		=> array(
				'label'			=> 'Language',
				'empty_option' 	=> 'Select language',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'text' 	=> array(
				'required' 	=> true,
				'filters' 	=> array(
					array(
						'name' => 'StringTrim',
					),
					array(
						'name' => 'StripTags',
					),
				),
			),
			'locale' => array(
				'required' => true,
			),
		);
	}
}
?>