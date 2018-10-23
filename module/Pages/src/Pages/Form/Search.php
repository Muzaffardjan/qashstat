<?php  
namespace Pages\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Search extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'search-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->setAttribute('method', 'GET');

		$this->add(array(
			'name' => 'search',
			'type' => 'Text',
			'attributes' => array(
				'required' 	=> true,
			),
			'options' => array(
				'label' => 'Search',
			),
		));

		$this->add(array(
			'name' => 'sort',
			'type' => 'Select',
			'options' => array(
				'label' 		=> 'Sort by',
				'empty_option' 	=> 'Relevance',
			),
		));

		$this->add(array(
			'name' => 'type',
			'type' => 'Select',
			'options' => array(
				'label' 		=> 'Type',
				'empty_option' 	=> 'All',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'search' => array(
				'required' 		=> true,
				'filters'		=> array(
					array(
						'name' => 'StripTags',
					),
					array(
						'name' => 'StringTrim',
					),
				),
				'validators' 	=> array(
					array(
						'name' => 'StringLength',
						'min'  => 3,
						'max'  => 512,
					),
				),
			),
			'sort' => array(
				'required' => false,
			),
			'type' => array(
				'required' => false,
			),
		);
	}
}
?>