<?php 
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
* Video form
*/	
class VideoForm extends Form implements InputFilterProviderInterface
{
	
	function __construct($name = null, $options = array())
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' => 'src',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Video',
			),
			'attributes' => array(
				'class' => 'form-control',
				'required' => 'required',
				'rows'     => 3,
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'src' => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'StringTrim',
					),
				),
			),
		);
	}
}
?>