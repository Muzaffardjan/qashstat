<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Events extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'pages-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' 			=> 'locale',
			'type' 			=> 'Select',
			'attributes' 	=> array(
				'required' => 'required',
			),
			'options' 		=> array(
				'label' => 'Language',
				'empty_option' => 'Select language',
			),
		));

		$this->add(array(
			'name' 			=> 'title',
			'type' 			=> 'Text',
			'attributes' 	=> array(
				'required' => 'required',
			),
			'options' 		=> array(
				'label' => 'Title',
			),
		));

		$this->add(array(
			'name' 		=> 'happens',
			'type' 		=> 'Text',
			'options' 	=> array(
				'label' => 'Happens',
			),
		));

		$this->add(array(
			'name' 		=> 'body',
			'type' 		=> 'Text',
			'options' 	=> array(
				'label' => 'Body',
			),
		));
	}

	public function addChainSelect($name, $label = null)
	{
		$this->add(array(
			'name' 		=> $name,
			'type' 		=> 'Select',
			'options' 	=> array(
				'label' 		=> $label,
				'empty_option' 	=> 'Select alternative',
			),
		));

		$this->getInputFilter()->add(array(
			'name' 		=> $name,
			'required' 	=> false,
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'locale' => array(
				'required' => true,
			),
			'title' => array(
				'required' => true,
				'filters'	=> array(
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
			'happens' => array(
				'required' => true,
				'filters'	=> array(
					array(
						'name' => 'StringTrim',
					),
				),
				'validators' => array(
					array(
						'name' => 'Date',
						'options'	=> array(
							'format' => 'd.m.Y H:i',
						),
					),
				),
			),
			'body' => array(
				'required' => false,
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