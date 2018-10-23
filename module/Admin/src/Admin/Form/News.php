<?php  
namespace Admin\Form;

use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class News extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'news-form', $options = null)
    {
        parent::__construct($name, $options);

        /** Category */
        $this->add([
            'name' => 'category',
            'type' => Select::class,
            'options' => [
                'label' => 'Category',
                'empty_option' => 'Select category',
                'disable_inarray_validator' => true,
            ],
            'attributes' => [
                'required' => 'required',
            ],
        ]);

        /** Title */
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

        /** Images */
        $this->add([
            'name' => 'image',
            'type' => Text::class,
            'options' => [
                'label' => 'Image',
            ],
        ]);

        /** Added */
        $this->add([
            'name' => 'added',
            'type' => Text::class,
            'options' => [
                'label' => 'Add time',
            ],
            'attributes' => [
                'required' => 'required',
            ],
        ]);

        /** Description */
        $this->add([
            'name' => 'description',
            'type' => Text::class,
            'attributes' => [
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Description',
            ],
        ]);

        /** Body */
        $this->add([
            'name' => 'body',
            'type' => Text::class,
            'attributes' => [
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Body',
            ],
        ]);
    }

    public function addChainSelect($name, $label = null)
    {
        $this->add(array(
            'name'      => $name,
            'type'      => 'Select',
            'options'   => array(
                'label'         => $label,
                'empty_option'  => 'Select alternative',
            ),
        ));

        $this->getInputFilter()->add(array(
            'name'      => $name,
            'required'  => false,
        ));
    }

    public function getInputFilterSpecification()
    {
        return [
            'category' => [
                'required' => true,
            ],
            'title' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            'image' => [
                'required'  => false,
                'filters'   => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Uri',
                    ],
                ],
            ],
            'added' => [
                'required' => true,
            ],
            'description' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
                'validators' => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'min' => 20,
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            'body' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ],
        ];
    }
}