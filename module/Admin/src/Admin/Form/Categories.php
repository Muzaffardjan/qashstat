<?php 
namespace Admin\Form;

use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\StringLength;

class Categories extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'categories-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add([
			'name' => 'title',
			'type' => Text::class,
            'options' => [
                'label' => 'Title',
            ],
			'attributes' => [
				'required' => 'required',
            ],
        ]);
	}

	public function getInputFilterSpecification()
	{
		return [
			'title' => [
				'required' => true,
				'filters'	=> [
					['name' => StringTrim::class],
					['name' => StripTags::class],
                ],
				'validators' => [
					[
						'name' => StringLength::class,
						'options' => [
							'max' => 255,
                        ],
                    ],
                ],
            ],
        ];
	}
}
?>