<?php  
namespace TypoReport\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Report extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'report-typo-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' 		=> 'url',
			'type' 		=> 'Text',
			'options' 	=> array(
				'label' => 'Url',
			),
		));

		$this->add(array(
			'name' 		=> 'text',
			'type' 		=> 'Text',
			'options' 	=> array(
				'label' => 'Typo',
			),
		));

		$this->add(array(
			'name' 		=> 'comment',
			'type' 		=> 'Text',
			'options' 	=> array(
				'label' => 'Comment',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'url' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'Uri',
					),
				),
			),
			'text' => array(
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
						'name' 		=> 'StringLength',
						'options' 	=> array(
							'min' => 4,
							'max' => 255,
						),
					),
				),
			),
			'comment' => array(
				'required' => false,
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
						'name' 		=> 'StringLength',
						'options' 	=> array(
							'max' => 255,
						),
					),
				),
			),
		);
	}
}
?>