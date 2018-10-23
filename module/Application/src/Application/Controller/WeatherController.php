<?php
/**
 * @author Muzaffardjan Karaev
 * @copyright (c) "FOR EACH SOFT" LTD 2015
 * Created: 16.01.2017
 */
namespace Application\Controller;

use Zend\Config\Config;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class WeatherController extends AbstractActionController
{
    public function citiesAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $city   = $this->params('city');
            $locale = $this->params('locale');

            $link = 'http://www.meteo.uz/api/v2/current-weather_' . substr($locale, 0, 2) . '.json';
            $json = file_get_contents($link);

            $data = \Zend\Json\Json::decode($json, true);
            $city = $data[$city];

            return new JsonModel([
                'city' => $city
            ]);
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function getallAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $locale = $this->params('locale');
            $link   = 'http://www.meteo.uz/api/v2/current-weather_' . substr($locale, 0, 2) . '.json';
            $json   = file_get_contents($link);

            $data = \Zend\Json\Json::decode($json, true);

            return new JsonModel(
                ['weather' => $data]
            );
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}