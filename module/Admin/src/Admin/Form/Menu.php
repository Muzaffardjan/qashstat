<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Menu extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'menu-form', $options = null)
    {
        parent::__construct($name, $options);

        $this->add(
            array(
                'name' => 'name',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Name',
                ),
                'attributes' => array(
                    'required' => 'required',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'locale',
                'type' => 'Select',
                'options' => array(
                    'label' => 'Language',
                    'empty_option' => 'Select language',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'position',
                'type' => 'Select',
                'options' => array(
                    'label' => 'Position',
                    'empty_option' => 'Hidden',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'structure',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Structure',
                ),
            )
        );
    }       

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 255,
                        ),
                    ),
                ),
            ),
            'locale' => array(
                'required' => true,
            ),
            'position' => array(
                'required' => false,
            ),
            'structure' => array(
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