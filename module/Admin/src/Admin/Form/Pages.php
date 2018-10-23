<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Pages extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'pages-form', $options = null)
    {
        parent::__construct($name, $options);

        $this->add(array(
            'name'          => 'locale',
            'type'          => 'Select',
            'attributes'    => array(
                'required' => 'required',
            ),
            'options'       => array(
                'label' => 'Language',
                'empty_option' => 'Select language',
            ),
        ));

        $this->add(array(
            'name'          => 'title',
            'type'          => 'Text',
            'attributes'    => array(
                'required' => 'required',
            ),
            'options'       => array(
                'label' => 'Title',
            ),
        ));

        $this->add(array(
            'name'      => 'body',
            'type'      => 'Text',
            'options'   => array(
                'label' => 'Body',
            ),
        ));

        $this->add(array(
            'name'      => 'visible',
            'type'      => 'Checkbox',
            'options'   => array(
                'label' => 'Visibility',
            ),
        ));

        $this->get('visible')->setChecked(true);
    }

    public function getInputFilterSpecification()
    {
        return array(
            'locale' => array(
                'required' => true,
            ),
            'title' => array(
                'required' => true,
                'filters'   => array(
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
                            'max' => 255,
                        ),
                    ),
                ),
            ),
            'body' => array(
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ),
            'visible' => array(
                'required' => true,
            ),
        );
    }
}