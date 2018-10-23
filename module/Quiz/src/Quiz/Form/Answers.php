<?php  
namespace Quiz\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Answers extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'answers-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' 			=> 'text',
			'type' 			=> 'Text',
			'attributes' 	=> array(
				'required' 	=> true,
			),
			'options'		=> array(
				'label'		=> 'Answer', 
			),
		));

		$this->add(array(
			'name' 			=> 'index',
			'type' 			=> 'Select',
			'options'		=> array(
				'label'			=> 'Order of answer', 
				'empty_option' 	=> 'Select order',
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
			'index'	=> array(
				'required' => false,
			),
		);
	}
}
?>