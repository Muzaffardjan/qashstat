<?php 
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
* Add image collection form
*/
class ImageCollection extends Form implements InputFilterProviderInterface
{

	function __construct($name = null, $options = array())
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' => 'collection-name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name of image collection',
			),
			'attributes' => array(
				'class' => 'form-control',
				'id'    => 'collection-name'
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'collection-name' => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'StringTrim',
					),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 6,
							'max' => 250
						),
					),
				),
			),
		);
	}
}
?>