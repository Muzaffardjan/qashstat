<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Captcha\Image as CaptchaImage;

class LoginUser extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'login-user', $options = null)
	{
		parent::__construct($name, $options);

		$captchaImage = new CaptchaImage(array(
				'font'				=> 'public/fonts/captcha.ttf',
                'width' 			=> 250,
                'height' 			=> 34,
                'dotNoiseLevel' 	=> 3,
                'lineNoiseLevel' 	=> 3,
                'fontSize'			=> 20,
                'wordlen'			=> 5,
            )
        );
        $captchaImage->setImgDir('public/img/captcha');
        $captchaImage->setImgUrl('img/captcha/');

		$this->add(array(
			'name' 			=> 'login',
			'type' 			=> 'Text',
			'options'		=> array(
				'label' 	=> 'Login',
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' 			=> 'password',
			'type' 			=> 'Password',
			'options'		=> array(
				'label' 	=> 'Password',
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
		));

		$this->add(array(
			'name' 			=> 'captcha',
			'type' 			=> 'Captcha',
			'options'		=> array(
				'label' 	=> 'Captcha',
				'captcha'	=> $captchaImage,
			),
			'attributes' 	=> array(
				'required' => 'required',
			),
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
			),
			'captcha' => array(
				'required' => true,
			),
		);
	}
}
?>