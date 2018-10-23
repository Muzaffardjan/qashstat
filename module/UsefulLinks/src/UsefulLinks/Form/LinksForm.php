<?php  
namespace UsefulLinks\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class LinksForm 
	  extends Form 
	  implements InputFilterProviderInterface
{
	public function __construct($name = 'useful-links-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name'			=> 'locale',
			'type'			=> 'Select',
			'attributes'	=> array(
				'required' => 1,
			),
			'options'		=> array(
				'label' 		=> 'Language',
				'empty_option' 	=> 'Select language',
			),
		));

		$this->add(array(
			'name'			=> 'url',
			'type'			=> 'Text',
			'attributes'	=> array(
				'required' => 1,
			),
			'options'		=> array(
				'label' 		=> 'URL',
			),
		));

		$this->add(array(
			'name'			=> 'title',
			'type'			=> 'Text',
			'attributes'	=> array(
				'required' => 1,
			),
			'options'		=> array(
				'label' 		=> 'Title',
			),
		));

		$this->add(array(
			'name'			=> 'image',
			'type'			=> 'Text',
			'attributes'	=> array(
				'required' => 1,
			),
			'options'		=> array(
				'label' 		=> 'Image',
			),
		));

		$this->add(array(
			'name'			=> 'order',
			'type'			=> 'Select',
			'options'		=> array(
				'label' 		=> 'Order',
				'empty_option' 	=> 'Change order',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'locale'	=> array(
				'required'	=> true,
			),
			'url'		=> array(
				'required'	=> true,
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
						'name' => 'Uri',
					),
					array(
						'name' 		=> 'StringLength',
						'options'	=> array(
							'max'	=> 1024,
						),
					),
				),
			),
			'title'		=> array(
				'required'	=> true,
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
						'name' 		=> 'StringLength',
						'options'	=> array(
							'max'	=> 256,
						),
					),
				),
			),
			'image'		=> array(
				'required'	=> true,
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
						'name' 		=> 'StringLength',
						'options'	=> array(
							'max'	=> 1024,
						),
					),
				),
			),
			'order'		=> array(
				'required'	=> false,
			),
		);
	}
}
?>