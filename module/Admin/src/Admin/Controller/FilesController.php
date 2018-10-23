<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Admin\FilesExplorer\FilesExplorer;

class FilesController extends AbstractActionController
{	
	public function uploadAction()
	{
		$request = $this->getRequest();
		$config 	= $this->getServiceLocator()->get('config');

		if(!$request->isPost())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$storage = realpath($config['pages']['files_upload']['private']) . '/';

    	if(!is_dir($storage . date('d-m-Y')))
    	{
    		mkdir($storage . date('d-m-Y'), 0755, true);
    	}


    	$storage 	= $storage . date('d-m-Y') . '/';
		$form 		= new \Admin\Form\FilesUpload();
		$translator = $this->getServiceLocator()->get('translator');

		$post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $form->setData($post);

    	foreach($form->getInputFilter()->get('uploads')->getFilterChain()->getFilters()->toArray() as $filter)
    	{
    		if($filter instanceof \Zend\Filter\File\RenameUpload)
    		{
    			$filter->setTarget($storage);
    		}
    	}

    	$validity = $form->isValid();

    	if($validity)
    	{
    		$formData = $form->getData();
    	}

		if($request->isXmlHttpRequest())
		{	
			$files = array();

			foreach($formData['uploads'] as $item)
			{
				$files[] = $item['tmp_name'];
			}

			if($validity)
			{
				return new JsonModel(array(
					'messages' 	=> array(
						$translator->translate('Upload ended successfully'),
						sprintf($translator->translate('Saved as %s'), join($files, ', <br>'))
					),
					'status' 	=> true,
				));
			}
			else
			{
				return new JsonModel(array(
					'messages' 	=> array_values(current(array_values($form->getMessages()))),
					'status'	=> false,
				));
			}
		}
	}

	public function fetchAction()
	{
		$request = $this->getRequest();

		if(!$request->isPost() || !$request->isXmlHttpRequest())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$config = $this->getServiceLocator()->get('config');
		$files = new FilesExplorer($config['files_explorer']);
		$result = $files->getPath($request->getPost('path'));

		return new JsonModel($result);
	}

	public function operateAction()
	{
		$request = $this->getRequest();
		$translator = $this->getServiceLocator()->get('translator');

		if(!$request->isPost() || !$request->isXmlHttpRequest())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$config = $this->getServiceLocator()->get('config');
		$files = new FilesExplorer($config['files_explorer']);

		switch($request->getPost('do'))
		{
			case 'new-folder': 
				if($request->getPost('path') && $request->getPost('name'))
				{
					try{
						$files->createFolder(trim($request->getPost('path')), trim($request->getPost('name')));
						goto success;
					}
					catch(\Exception $e)
					{
						return new JsonModel(array(
							'status' 	=> false,
							'messages' 	=> array(
								$e->getMessage()
							),
						));
					}
				}
				else
				{
					goto error;
				}
			break; 
			case 'delete': 
				if($request->getPost('source'))
				{
					try{
						$files->delete(trim($request->getPost('source')));
						goto success;
					}
					catch(\Exception $e)
					{
						return new JsonModel(array(
							'status' 	=> false,
							'messages' 	=> array(
								$e->getMessage()
							),
						));
					}
				}
				else
				{
					goto error;
				}
			break;
			case 'move': 
				if($request->getPost('from') && $request->getPost('to'))
				{
					try{
						$files->move(trim($request->getPost('from')), trim($request->getPost('to')));
						goto success;
					}
					catch(\Exception $e)
					{
						return new JsonModel(array(
							'status' 	=> false,
							'messages' 	=> array(
								$e->getMessage()
							),
						));
					}
				}
				else
				{
					goto error;
				}
			break;
			case 'rename': 
				if($request->getPost('source') && $request->getPost('name'))
				{
					try{
						$files->rename(trim($request->getPost('source')), trim($request->getPost('name')));
						goto success;
					}
					catch(\Exception $e)
					{
						return new JsonModel(array(
							'status' 	=> false,
							'messages' 	=> array(
								$e->getMessage()
							),
						));
					}
				}
				else
				{
					goto error;
				}
			break;
			case 'success':
				success:
				return new JsonModel(array(
					'status' 	=> true,
					'messages' 	=> array(
						$translator->translate('Done')
					),
				));
			break;
			default:
				error:
				return new JsonModel(array(
					'status' 	=> false,
					'messages' 	=> array(
						$translator->translate('Bad request')
					),
				));
		}
	}

	public function jsTreeAction()
	{
		$request = $this->getRequest();
		$config = $this->getServiceLocator()->get('config');
		$files = new FilesExplorer($config['files_explorer']);

		if(!$request->isPost() || !$request->isXmlHttpRequest())
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if($request->getPost('id') && $request->getPost('id') != '#')
		{
			$path = $request->getPost('id');
		}
		else
		{
			$path = '/';
		}

		$result = $files->getPath($path, array('name' => 'text'));

		foreach($result as $key => $value)
		{
			if(isset($value['children']))
			{
				$result[$key]['children'] = (bool) $value['children'];
				$result[$key]['icon'] = 'fa fa-folder';
			}
			else
			{
				$result[$key]['children'] = false;
				$result[$key]['icon'] = 'fa fa-file';
			}

			$result[$key]['id'] = $value['path'];
		}

		$result = array_values($result);

		if($path == '/')
		{
			$result = array(
				'text' 		=> '/',
				'id'		=> '/',
				'path'		=> '/',
				'icon'		=> 'fa fa-folder',
				'type'		=> 'root',
				'children' 	=> $result,
			);
		}

		return new JsonModel($result);
	}
}
?>