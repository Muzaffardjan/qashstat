<?php

namespace Feedback\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Feedback extends Form implements InputFilterProviderInterface
{
	public function __construct($name = null)
	{
		parent::__construct('Feedback');
		$this->setAttribute('method', 'post');

		$this->add(array(
			'name' => 'name',
			'attributes' => array(
				'type' => 'text',
				'required' => 'required',
				'class' => "form-control form-input",
				'aria-required' => 'true',
				'size'	=> '30',
				'id' => 'name',
			),
			'options' => array(
				'label' => 'Full name',
			),
		));

		$this->add(array(
			'name' => 'phone',
			'attributes' => array(
				'type' => 'text',
				'required' => 'required',
				'class' => "form-control form-input",
			),
			'options' => array(
				'label' => 'Phone',
			),
		));

		$this->add(array(
			'name' => 'email',
			'attributes' => array(
				'type' => 'email',
				'required' => 'required',
				'class' => "form-control form-input",
			),
			'options' => array(
				'label' => 'Email',
			),
		));

		$this->add(array(
			'name' => 'subject',
			'attributes' => array(
				'type' => 'text',
				'required' => 'required',
				'class' => "form-control form-input",

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
				'class' => "form-control form-input",
				'rows' => 10,
				'area-required' => 'true',
				'rows' => '8',
				'cols' => '45',
				'id' => 'message',
			),
			'options' => array(
				'label' => 'Text',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'name' => array(
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
	    						'max' => 140,
	    					),
	    				),
	    			),
	    		),
			'phone' => array(
	    			'required' => true,
	    			'filters' => array(
	    				array('name' => 'StringTrim'),
	    				array('name' => 'StripTags'),
	    			),
	    			'validators' => array(
	    				array(
	    					'name' => 'StringLength',
	    					'options' => array(
	    						'min' => 13,
	    						'max' => 32,
	    					),
	    				),
	    				array(
	    					'name' 		=> 'Regex',
	    					'options' 	=> array(
	    						'pattern' 	=> '/^\+998[0-9]{9}$/',
	    						'messages' 	=> array(
	    							\Zend\Validator\Regex::NOT_MATCH => 'Telephone number format must be like: +998XXYYYYYYY',
	    						),
	    					),
	    				),
	    			),
	    		),
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
	    						'max' => 255,
	    					),
	    				),
	    			),
	    		),
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