<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class AlternativesChain extends Form
{
	public function __construct($name = 'alternatives-chain', $options = null)
	{
		parent::__construct($name, $options);
	}

	public function addChainSelect($name, $label = null)
	{
		$this->add(array(
			'name' 		=> $name,
			'type' 		=> 'Select'
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
}
?>