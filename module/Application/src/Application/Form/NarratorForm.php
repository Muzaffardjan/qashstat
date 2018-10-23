<?php 
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class NarratorForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        
        $this->add(
            [
                'name' => 'text-to-speak',
                'type' => 'Zend\Form\Element\Text'
            ]
        );

        $this->add(
            [
                'name' => 'csrf-token',
                'type' => 'Zend\Form\Element\Text'
            ]
        );

        $this->add(
            [
                'name' => 'action-id',
                'type' => 'Zend\Form\Element\Text'
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'text-to-speak' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    ['name' => 'NotEmpty']
                ]
            ],
            'csrf-token' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 32,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'action-id' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => ['pattern' => '/^A[0-9]{3}$/']
                    ]
                ]
            ]
        ];
    }
}
?>