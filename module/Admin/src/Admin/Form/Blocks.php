<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Blocks extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'blocks-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' 		=> 'content',
			'type' 		=> 'Text',
			'options' 	=> array(
				'label' => 'Content',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'content' => array(
				'required' => true,
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