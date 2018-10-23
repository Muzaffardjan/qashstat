<?php 
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
* Form for FineUploader
*/
class FineUploader extends Form implements InputFilterProviderInterface
{
	
	function __construct($name = null, $options = array())
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' => 'qquuid',
			'type' => 'Text',
		));

		$this->add(array(
			'name' => 'qqfilename',
			'type' => 'Text',
		));

		$this->add(array(
			'name' => 'qqtotalfilesize',
			'type' => 'Text',
		));

		$this->add(array(
			'name' => 'qqfile',
			'type' => 'File',
		));
	}

	public function getInputFilterSpecification()
	{
		$session      = new \Zend\Session\Container('Media');
		$moduleConfig = include __DIR__ . '/../../../config/module.config.php'; 
		$mediaConfig  = $moduleConfig['media'];
 
		return array(
			'qquuid' => array(
				'required' => true,
				'filters'  => array(
					array(
						'name' => 'StringTrim',
					),
					array(
						'name' => 'StripTags',
					),
				),
			),
			'qqfilename' => array(
				'required' => true,
				'filters'  => array(
					array(
						'name' => 'StringTrim',
					),
					array(
						'name' => 'StripTags',
					),
				),
			),
			'qqtotalfilesize' => array(
				'required' => true,
				'filters'  => array(
					array(
						'name' => 'StringTrim',
					),
					array(
						'name' => 'StripTags',
					),
				),
			),
			'qqfile' => array(
				'required' => true,
				'filters'  => array(
					array(
						'name' => 'File\RenameUpload',
						'options' => array(
							'target' => $mediaConfig['save_path'] . "collection_" . $session->collection . "/",
							'use_upload_name' => true,
							'use_upload_extension' => true,
							'overwrite' => false,
							'randomize' => false,
						),
					),
				),
				'validators' => array(
					array(
						'name' => 'File\Extension',
						'options' => array(
							'extension' => $mediaConfig['allowed_extensions'],
						),
					),
					array(
						'name' => 'File\Size',
						'options' => array(
							'min' => $mediaConfig['min_file_size'],
							'max' => $mediaConfig['max_file_size'],
						),
					),
				),
			),	
		);
	}
}
?>