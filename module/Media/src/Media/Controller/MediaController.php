<?php

namespace Media\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MediaController extends AbstractActionController
{
    public function videosAction()
    {
        //$this->layout('layout/media');
        $videosTable = $this->getServiceLocator()->get('Media\Tables\Videos');
    	$descriptionsTable = $this->getServiceLocator()->get('Media\Tables\VideoDescriptions');
        $allDesc = $descriptionsTable->fetchAll(array('locale' => $this->params('locale')));
        $ids = array();
        $descriptions = array();

        foreach($allDesc as $description)
        {
            $descriptions[$description->video_id] = $description;
            $ids[] = $description->video_id;
        }

        $videos = array();

        if($ids)
        {
            $allVideos = $videosTable->getWith($ids);

            foreach($allVideos as $video)
            {
                $videos[$video->id] = $video;
            }
        }

    	return array(
            'videos'        => $videos,
    		'descriptions'  => $descriptions,
    	);
    }

    public function galleriesAction()
    {
        //$this->layout('layout/media');
        $galleriesTable = $this->getServiceLocator()->get('Media\Tables\Galleries');
        $imgColTable    = $this->getServiceLocator()->get('Media\Tables\ImageCollection');
        $galleries      = $galleriesTable->fetchAll(array('locale' => $this->params('locale')));

        $collections = $imgColTable->fetchAll();
        $images = array();

        foreach($collections as $item)
        {
            $images[$item->name][] = $item;
        }

        return array(
            'galleries' => $galleries,
            'images'    => $images
        );
    }

    public function galleryAction()
    {
        //$this->layout('layout/media');

    	$galleryUrl = $this->params()->fromRoute('gallery');
        $galleriesTable = $this->getServiceLocator()->get('Media\Tables\Galleries');
        $imgColTable    = $this->getServiceLocator()->get('Media\Tables\ImageCollection');
    	$imgDescTable   = $this->getServiceLocator()->get('Media\Tables\ImageDescriptions');

        $gallery = $galleriesTable->getWith(array('url' => $galleryUrl))->current();

        if ( ! $gallery)
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $imgCollection = $imgColTable->getByName($gallery->images);

        $temp = array();
        $ids = array();

        foreach($imgCollection as $image)
        {
            $temp[] = $image;
            $ids[] = $image->id;
        }

        $imgCollection = $temp;

        $imagesDescription = array();

        foreach($imgDescTable->getWith(array('image_id' => $ids, 'locale' => $this->params('locale'))) as $desc)
        {
            $imagesDescription[$desc->image_id] = $desc;
        }

        return array(
            'gallery'       => $gallery,
            'images'        => $imgCollection,
            'descriptions'  => $imagesDescription,
        );
    }
}
