<?php 
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
* Description form
*/	
class ImgDescriptionForm extends Form implements InputFilterProviderInterface
{
	
	function __construct($name = null, $options = array())
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' => 'locale',
			'type' => 'Select',
			'options' => array(
				'label' => 'Language',
				'empty_option' => 'Select language',
			),
			'attributes' => array(
				'class' => 'form-control',
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' => 'title',
			'type' => 'Text',
			'options' => array(
				'label' => 'Image title'
			),
			'attributes' => array(
				'required' => 'required',
				'class' => 'form-control',
			),
		));

		$this->add(array(
			'name' => 'text',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Description',
			),
			'attributes' => array(
				'class' => 'form-control',
				'rows' => 3,
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'locale' => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'StringTrim',
					),
				),
			),
			'title' => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'StringTrim',
					),
					array(
						'name' => 'StripTags',
					),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'max' => 64,
						),
					),
				),
			),
			'text' => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StringTrim',
					),
					array(
						'name' => 'StripTags',
						'options' => array(
							'allowTags' => array('strong', 'em', 'u', 'b', 'i')
						),
					),
				),
			),
		);
	}
}
?>