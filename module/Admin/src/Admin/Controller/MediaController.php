<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class MediaController extends AbstractActionController
{
	protected $session;

	public function onDispatch(\Zend\Mvc\MvcEvent $e)
	{
		$this->session = new \Zend\Session\Container('Media');
		$this->createFolders();
		$this->deleteEmptyFolders();

		parent::onDispatch($e);
	}

	public function indexAction()
	{
		unset($this->session->collection);
		$this->layout('layout/admin');
		
		return array();
	}

	public function galleriesAction()
	{
		$this->layout('layout/admin');
		$galleriesTable = $this->getServiceLocator()->get('Media\Tables\Galleries');

		$galleries = $galleriesTable->fetchAll();

		return array(
			'galleries' => $galleries,
		);
	}

	public function addGalleryAction()
	{
		$this->layout('layout/admin');
		$form = new \Media\Form\GalleryForm();
		$config  = $this->getServiceLocator()->get('config');
		$request = $this->getRequest();

		$form->get('locale')->setValueOptions($config['translator']['locales']);

		$resultSet = $this->getServiceLocator()->get('Media\Tables\ImageCollection')->getCollections();
		$valueOptions = array();

		foreach ($resultSet as $collection)
		{
			$valueOptions[$collection->name] = $collection->name;
		}

		$form->get('images')->setValueOptions($valueOptions);

		if ($request->isPost())
		{
			$form->setData($request->getPost());

			if ($form->isValid())
			{
				$gallery = new \Media\ArrayObject\Gallery();
				$gallery->exchangeArray($form->getData());

				$gallery->url = \Application\Library\Stdlib::get_url_string($gallery->title);

				$this->getServiceLocator()->get('Media\Tables\Galleries')->add($gallery);

				return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'galleries'));
			}
		}

		return array(
			'form' => $form,
		);
	}

	public function editGalleryAction()
	{
		$this->layout('layout/admin');
		$form = new \Media\Form\GalleryForm();
		$config  = $this->getServiceLocator()->get('config');
		$request = $this->getRequest();

		$form->get('locale')->setValueOptions($config['translator']['locales']);

		$resultSet = $this->getServiceLocator()->get('Media\Tables\ImageCollection')->getCollections();
		$valueOptions = array();

		foreach ($resultSet as $collection)
		{
			$valueOptions[$collection->name] = $collection->name;
		}

		$form->get('images')->setValueOptions($valueOptions);
		
		$galleriesTable = $this->getServiceLocator()->get('Media\Tables\Galleries');
		$gallery        = $galleriesTable->getGalleryById($this->params('collection'));
		
		if ( ! $gallery)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$form->setData($gallery->getData());

		if ($request->isPost())
		{
			$form->setData($request->getPost());

			if ($form->isValid())
			{
				$gallery->exchangeArray($form->getData());
				$galleriesTable->edit($gallery);

				return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'galleries'));
			}
		}

		return array(
			'form' => $form,
		);	
	}

	public function deleteGalleryAction()
	{
		$this->layout('layout/admin');
		$request = $this->getRequest();
		$galleriesTable = $this->getServiceLocator()->get('Media\Tables\Galleries');

		$gallery = $galleriesTable->getGalleryById($this->params('collection'));

		if ( ! $gallery)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if ($request->isPost())
		{
			$galleriesTable->delete($gallery->id);

			return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'galleries'));
		}

		return array(
			'gallery' => $gallery,
		);
	}

	public function collectionsAction()
	{
		$this->layout('layout/admin');
		$imgColTable = $this->getServiceLocator()->get('\Media\Tables\ImageCollection');
		$collections = $imgColTable->getCollections();

		return array(
			'collections' => $collections
		);
	}	

	public function addImageCollectionAction()
	{	
		$this->layout('layout/admin');
		$imgCollectionForm = new \Media\Form\ImageCollection();

		return array(
			'imgCollectionForm' => $imgCollectionForm,
		);
	}

	public function editImageCollectionAction()
	{
		$this->layout('layout/admin');
		$collectionName = $this->params()->fromRoute('collection');
		$imgColTable    = $this->getServiceLocator()->get('Media\Tables\ImageCollection');

		$collection = $imgColTable->getByName($collectionName);

		if ($collection->count() == 0)
		{
			return $this->deleteImageCollectionAction(false);
		}
		
		$this->session->collection = $collectionName;

		return array(
			'collection' => $collection
		);
	}

	/**
	 * Delete image collection from database and gallery folder
	 * @param bool $askFromUser ask from user before delete
	 * @return void
	 */
	public function deleteImageCollectionAction($askFromUser = true)
	{
		$this->layout('layout/admin');
		$collectionName = $this->params()->fromRoute('collection');
		$imgColTable    = $this->getServiceLocator()->get('Media\Tables\ImageCollection');
		$galleriesTable = $this->getServiceLocator()->get('Media\Tables\Galleries');
		
		if ( ! $askFromUser || $this->getRequest()->isPost())
		{
			$mediaConfig = $this->getServiceLocator()->get('config')['media'];

			if ($askFromUser)
			{
				$imgIdList = array();
				foreach ($imgColTable->getByName($collectionName) as $image)
				{
					$imgIdList[] = $image->id;
				}

				// Delete from image collections table
				$imgColTable->delete(array('name' => $collectionName));

				// Delete image descriptions
				$this->getServiceLocator()->get('Media\Tables\ImageDescriptions')->delete(array(
					'image_id' => $imgIdList,
				));
			}

			// Delete from galleries table
			$galleriesTable->delete(array('images' => $collectionName));

			// Delete files from gallery folder
			$directory = $mediaConfig['save_path'] . "collection_" . $collectionName;

			if (file_exists($directory))
			{
				\Media\Model\DirectoryHelper::deleteDirectory($directory);
			}

			return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'collections'));
		}

		$collection = $imgColTable->getByName($collectionName);
		
		if ($collection->count() == 0)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$gallery    = $galleriesTable->getWith(array('images' => $collectionName));

		return array(
			'collectionName' => $collectionName,
			'collection' => $collection,
			'gallery'    => $gallery,
		);
	}

	public function uploadAction()
	{
		$request = $this->getRequest();
		
		if ($request->isPost() && $request->isXmlHttpRequest())
		{
			$mediaConfig = $this->getServiceLocator()->get('config')['media'];

			if ($this->session->collection)
			{
				$saveDir = $mediaConfig['save_path'] . "collection_" . $this->session->collection;
				if (file_exists($saveDir))
				{
					$uploadForm = new \Media\Form\FineUploader();
					$uploadForm->setData(
						array_merge_recursive(
							$request->getPost()->toArray(),
							$request->getFiles()->toArray()
						)
					);

					if ($uploadForm->isValid())
					{
						$formData = $uploadForm->getData();
						$imgColTable = $this->getServiceLocator()->get('Media\Tables\ImageCollection');
						$image = new \Media\ArrayObject\Image;
						$image->name = $this->session->collection;
						$image->src  = basename($formData['qqfile']['tmp_name']);

						$imgColTable->add($image);
						return new JsonModel(array("success" => true));
					}
					else
					{
						$messages = array_values(current($uploadForm->getMessages()));
						$error = array_values(current($messages));

						return new JsonModel(array(
							"success" => false,
							"error"  => $error 
						));
					}
				}
			}
		}

		$this->getResponse()->setStatusCode(404);		
	}

	public function editImageAction()
	{
		$this->layout('layout/admin');
		$request = $this->getRequest();

		$collectionName = $this->params()->fromRoute('collection');
		$imgId = $this->params()->fromRoute('id');

		$image = $this->getServiceLocator()->get('Media\Tables\ImageCollection')->getWith(array('name' => $collectionName, 'id' => $imgId))->current();

		if ($image)
		{
			$config       = $this->getServiceLocator()->get('config');
			$imgDescTable = $this->getServiceLocator()->get('Media\Tables\ImageDescriptions');
			$resultSet    = $imgDescTable->getByImageId($image->id);
			$descForm     = new \Media\Form\ImgDescriptionForm();

			$descriptions = array();
			$locales      = $config['translator']['locales'];
			
			foreach ($resultSet as $desc)
			{
				$desc->localeName = $locales[$desc->locale];
				$descriptions[] = $desc;

				unset($locales[$desc->locale]);
			}

			$descForm->get('locale')->setValueOptions($locales);

			if ($request->isPost())
			{
				$descForm->setData($request->getPost());

				if ($descForm->isValid())
				{
					$formData = $descForm->getData();

					if ($formData['text'] == '') $formData['text'] = null;

					$description = new \Media\ArrayObject\ImageDescription();
					$description->setData($formData);
					$description->image_id = $imgId;

					$imgDescTable->add($description);

					return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'editImage', 'collection' => $collectionName, 'id' => $imgId));
				}
			}

			return array(
				'image'   => $image,
				'locales' => $locales,
				'form'    => $descForm,
				'descriptions' => $descriptions,
			);
		}

		$this->getResponse()->setStatusCode(404);
	}

	public function deleteImageAction()
	{
		$this->layout('layout/admin');
		$request = $this->getRequest();

		$collectionName = $this->params()->fromRoute('collection');
		$imgId = $this->params()->fromRoute('id');

		if ($collectionName && $imgId)
		{
			$mediaConfig = $this->getServiceLocator()->get('config')['media'];
			$imgColTable    = $this->getServiceLocator()->get('Media\Tables\ImageCollection');
			$collection 	= $imgColTable->getWith(array('name' => $collectionName, 'id' => $imgId))->current();

			if ($collection && $collection->name == $collectionName)
			{
				if ($request->isPost())
				{
					$imgColTable->delete($collection->id);
					$this->getServiceLocator()->get('Media\Tables\ImageDescriptions')->delete(array('image_id' => $imgId));
					
					if(is_file($mediaConfig['save_path'] . "collection_" . $collection->name . "/" . $collection->src))
					{
						unlink($mediaConfig['save_path'] . "collection_" . $collection->name . "/" . $collection->src);
					}

					return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'editImageCollection', 'collection' => $collection->name));
				}

				return array(
					'collection' => $collection
				);
			}
		}

		$this->getResponse()->setStatusCode(404);
	}

	public function deleteImageDescriptionAction()
	{
		$imgId  = $this->params()->fromRoute('collection');
		$descId = $this->params()->fromRoute('id');

		$image = $this->getServiceLocator()->get('Media\Tables\ImageCollection')->getImageById($imgId);

		if ( ! $image)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$this->getServiceLocator()->get('Media\Tables\ImageDescriptions')->delete(array(
			'image_id' => $imgId,
			'id' => $descId,
		));

		return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'editImage', 'collection' => $image->name, 'id' => $image->id));
	}

	public function checkCollectionNameAction()
	{
		$request = $this->getRequest();

		if ($request->isPost() && $request->isXmlHttpRequest())
		{
			$imgCollectionForm = new \Media\Form\ImageCollection();
			$imgCollectionForm->setData($request->getPost());

			if ($imgCollectionForm->isValid())
			{
				$mediaConfig = $this->getServiceLocator()->get('config')['media'];
				$translator  = $this->getServiceLocator()->get('translator');
				$imgColTable = $this->getServiceLocator()->get('Media\Tables\ImageCollection');
				$formData    = $imgCollectionForm->getData();
				$saveDir     = $mediaConfig['save_path'] . "collection_" . $formData['collection-name'];

				// Check if image collection with this name is exists in database
				if ($imgColTable->getByName($formData['collection-name'])->count() == 0)
				{
					// Checking directories in upload collections' folder
					if ( ! file_exists($saveDir))
					{
						// Making collection directory
						mkdir($saveDir, 0755);

						// Establishing collection name to session storage
						$this->session->collection = $formData['collection-name'];
						return new JsonModel(array("status" => "success"));
					}
				}

				return new JsonModel(array(
					'status' => 'error',
					'error'  => $translator->translate('Collection with this name is exists'),
				));
			}
			else
			{
				$messages = $imgCollectionForm->getMessages();
				$error = current(array_values($messages['collection-name']));
				return new JsonModel(array(
					'status' => 'error',
					'error' => $error
				)); 
			}
		}

		$this->getResponse()->setStatusCode(404);
	}

	public function videosAction()
	{
		$this->layout('layout/admin');
		$videosTable = $this->getServiceLocator()->get('Media\Tables\Videos');
		$videos = $videosTable->fetchAll();

		return array(
			'videos' => $videos,
		);
	}

	public function addVideoAction()
	{
		$this->layout('layout/admin');
		$form = new \Media\Form\VideoForm(); 

		if ($this->getRequest()->isPost())
		{
			$form->setData($this->getRequest()->getPost());

			if ($form->isValid())
			{
				$video = new \Media\ArrayObject\Video();
				$video->setData($form->getData());
				$video->time = time();

				$videosTable = $this->getServiceLocator()->get('Media\Tables\Videos');
				$videosTable->add($video);

				return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'videos'));
			}
		}

		return array(
			'form' => $form,
		);
	}

	public function editVideoAction()
	{
		$this->layout('layout/admin');
		$videoId = $this->params()->fromRoute('collection');
		$videosTable = $this->getServiceLocator()->get('Media\Tables\Videos');
		$descsTable  = $this->getServiceLocator()->get('Media\Tables\VideoDescriptions');
		$form        = new \Media\Form\DescriptionForm();

		$video = $videosTable->getVideoById($videoId);

		if ( ! $video)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$config  = $this->getServiceLocator()->get('config');
		$locales = $config['translator']['locales'];

		$resultSet = $descsTable->getByVideoId($videoId);
		$descriptions = array();

		foreach ($resultSet as $desc)
		{
			$locale = $desc->locale;
			$desc->locale = $locales[$locale];
			$descriptions[] = $desc;

			unset($locales[$locale]);
		}

		$form->get('locale')->setValueOptions($locales);

		if ($this->getRequest()->isPost())
		{
			$form->setData($this->getRequest()->getPost());

			if ($form->isValid())
			{
				$description = new \Media\ArrayObject\VideoDescription();
				$description->setData($form->getData());
				$description->video_id = $videoId;

				$descsTable->add($description);

				return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'editVideo', 'collection' => $videoId));
			}
		}

		return array(
			'form'  => $form,
			'video' => $video,
			'descriptions' => $descriptions,
			'locales' => $locales
		);
	}

	public function deleteVideoAction()
	{
		$this->layout('layout/admin');
		$videoId = $this->params()->fromRoute('collection');
		$videosTable = $this->getServiceLocator()->get('Media\Tables\Videos');
		$video       = $videosTable->getVideoById($videoId);

		if ( ! $video)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if ($this->getRequest()->isPost())
		{
			$videosTable->delete($video->id);
			$this->getServiceLocator()->get('Media\Tables\VideoDescriptions')->delete(array('video_id' => $video->id));

			return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'videos'));
		}

		return array(
			'video' => $video,
			'descriptions' => $this->getServiceLocator()->get('Media\Tables\VideoDescriptions')->getByVideoId($videoId),
		);
	}

	public function deleteVideoDescriptionAction()
	{
		$videoId = $this->params()->fromRoute('collection');
		$descId  = $this->params()->fromRoute('id');

		$this->getServiceLocator()->get('Media\Tables\VideoDescriptions')->delete(array(
			'id' => $descId,
			'video_id' => $videoId,
		));

		return $this->redirect()->toRoute('admin/media', array('locale' => $this->params('locale'), 'action' => 'editVideo', 'collection' => $videoId));
	}

	/**
	 * Check existance of media directories and create them if not exist
	 */
	private function createFolders()
	{
		$mediaConfig = $this->getServiceLocator()->get('config')['media'];
		$saveDir = rtrim($mediaConfig['save_path'], '\\/');

		if ( ! file_exists($saveDir))
		{
			mkdir($saveDir, 0755, true);
		}
	}

	/**
	 * Delete empty collection folders
	 */
	private function deleteEmptyFolders()
	{
		$mediaConfig = $this->getServiceLocator()->get('config')['media'];
		$saveDir = rtrim($mediaConfig['save_path']);
		$now = time();

		$iterator = new \DirectoryIterator($saveDir);

		foreach ($iterator as $fileInfo)
		{
			if ($fileInfo->isDot()) continue;

			if ($fileInfo->isDir() && \Media\Model\DirectoryHelper::isEmpty($fileInfo->getPathname()))
			{
				if ($fileInfo->getMTime() + $mediaConfig['time_to_live'] < $now)
				{
					\Media\Model\DirectoryHelper::deleteDirectory($fileInfo->getPathname());
				}
			}
		}
	}
}
?>