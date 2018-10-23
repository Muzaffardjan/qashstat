<?php 
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
* Gallery form
*/	
class GalleryForm extends Form implements InputFilterProviderInterface
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
				'label' => 'Title',
			),
			'attributes' => array(
				'class' => 'form-control',
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' => 'images',
			'type' => 'Select',
			'options' => array(
				'label' => 'Image collection',
				'empty_option' => 'Select image collection',
			),
			'attributes' => array(
				'class' => 'form-control',
				'required' => 'required',
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
					array(
						'name' => 'StripTags',
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
							'max' => 255,
						),
					),
				),
			),
			'images' => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'StringTrim',
					),
					array(
						'name' => 'StripTags',
					),
				),
			),
		);
	}
}
?>