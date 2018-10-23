<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class RegisterUser extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'register-user', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' 			=> 'login',
			'type' 			=> 'Text',
			'options' 		=> array(
				'label' => 'Login',
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' 			=> 'password',
			'type' 			=> 'Password',
			'options' 		=> array(
				'label' => 'Password',
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' 			=> 'password-check',
			'type' 			=> 'Password',
			'options' 		=> array(
				'label' => 'Repeat password',
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' 			=> 'name',
			'type' 			=> 'Text',
			'options' 		=> array(
				'label' => 'Full name',
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' 			=> 'description',
			'options' 		=> array(
				'label' => 'Description',
			),
			'type' 			=> 'Text',
		));
	}

	public function addRolesSelect()
	{
		$this->add(array(
			'name' 			=> 'roles',
			'type' 			=> 'Select',
			'options' 		=> array(
				'label' => 'Role',
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
		));

		$this->getInputFilter()->add(array(
			'name' 		=> 'roles',
			'required'	=> true,
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'login' => array(
				'required' 	=> true,
				'filters' 	=> array(
					array(
						'name' => 'StringTrim',
					),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 4,
							'max' => 64,
						),
					),
				),
			),
			'password' => array(
				'required' 	=> true,
				'filters' 	=> array(
					array(
						'name' => 'StringTrim',
					),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 7,
						),
					),
				),
			),
			'password-check' => array(
				'required' 	=> true,
				'filters' 	=> array(
					array(
						'name' => 'StringTrim',
					),
				),
				'validators' => array(
					array(
						'name' => 'Identical',
						'options' => array(
			                'token' => 'password',
			            ),
					),
				),
			),
			'name' => array(
				'required' 	=> true,
				'filters' 	=> array(
					array(
						'name' => 'StringTrim',
					),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 3,
							'max' => 128,
						),
					),
				),
			),
			'description' => array(
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
						'name' => 'StringLength',
						'options' => array(
							'max' => 256,
						),
					),
				),
			),
		);
	}
}
?>