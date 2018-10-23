<?php

namespace Feedback\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Answer extends Form implements InputFilterProviderInterface
{
	public function __construct($name = null)
	{
		parent::__construct('Answer');
		$this->setAttribute('method', 'post');

		$this->add(array(
			'name' => 'subject',
			'attributes' => array(
				'type' => 'text',
				'required' => 'required',
				'class' => "form-control",

			),
			'options' => array(
				'label' => 'Subject',
			),
		));

		$this->add(array(
			'name' => 'text',
			'attributes' => array(
				'type' => 'textarea',
				'required' => 'required',
				'class' => "form-control",
				'rows' => 5,
			),
			'options' => array(
				'label' => 'Text',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'subject' => array(
	    			'required' => true,
	    			'filters' => array(
	    				array('name' => 'StringTrim'),
	    				array('name' => 'StripTags'),
	    			),
	    			'validators' => array(
	    				array(
	    					'name' => 'StringLength',
	    					'options' => array(
	    						'min' => 4,
	    						'max' => 255,
	    					),
	    				),
	    			),
	    		),
			'text' => array(
	    			'required' => true,
	    			'filters' => array(
	    				array('name' => 'StringTrim'),
	    				array('name' => 'StripTags'),
	    			),
	    			'validators' => array(
	    				array(
	    					'name' => 'StringLength',
	    					'options' => array(
	    						'min' => 10,
	    					),
	    				),
	    			),
	    		),
		);
	}
}

?>