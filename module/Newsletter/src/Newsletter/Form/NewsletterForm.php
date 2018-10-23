<?php
namespace Newsletter\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class NewsletterForm extends Form implements InputFilterProviderInterface
{
	
	public function __construct($name = null)
	{
		parent::__construct('newsletter');

		$this->add(
			array(
				'name' => 'email',
				'attributes' => array(
					'type' => 'email',
					'reqired' => 'required',
				),
				'options' => array(
					'label' => 'Enter your email',
				),
			)
		);
	}

	public function getInputFilterSpecification()
	{
		return array(
				'email' => array(
					'required' => true,
					'filters' => array(
						array('name' => 'StringTrim'),
						array('name' => 'StripTags'),
					),
					'validators' => array(
						array(
							'name' => 'StringLength',
							'options' => array(
								'min' => 5,
								'max' => 50,
							),
						),
				),
			),
		);
	}
}
?>