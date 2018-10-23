<?php  
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class FilesUpload extends Form implements InputFilterProviderInterface
{
	public function __construct($name = 'files-form', $options = null)
	{
		parent::__construct($name, $options);

		$this->add(array(
			'name' => 'uploads',
			'type' => 'File',
			'attributes' => array(
				'multiple' => true,
				'label' => 'Files upload',
			),
		));
	}

	public function getInputFilterSpecification()
	{
		return array(
			'uploads' => array(
				'required' => true,
				'filters'	=> array(
					array(
						'name' => 'File\RenameUpload',
						'options'	=> array(
							'use_upload_name' 		=> true,
							'use_upload_extension' 	=> true,
							'overwrite' 			=> true,
						),
					),
				),
				'validators' => array(
					array(
						'name' => 'File\Size',
						'options' => array(
							'min' => '2KB',
							'max' => $this->file_upload_max_size(),
						),
					),
					array(
						'name' => 'File\ExcludeExtension',
						'options' => array(
							'php', 'phtml', 'html', 'shtml', 'htm',
						),
					),
				),
			),
		);
	}

	public function file_upload_max_size() {
		static $max_size = -1;

		if ($max_size < 0) {
			// Start with post_max_size.
			$max_size = $this->parse_size(ini_get('post_max_size'));

			// If upload_max_size is less, then reduce. Except if upload_max_size is
			// zero, which indicates no limit.
			$upload_max = $this->parse_size(ini_get('upload_max_filesize'));
			if ($upload_max > 0 && $upload_max < $max_size) {
				$max_size = $upload_max;
			}
		}
		return $max_size;
	}

	protected function parse_size($size) {
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
		if ($unit) {
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		}
		else {
			return round($size);
		}
	}
}
?>