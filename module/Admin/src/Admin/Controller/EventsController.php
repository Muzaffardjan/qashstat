<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class EventsController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout('layout/admin');
        $tableEvents = $this->getServiceLocator()->get('Pages\Tables\Events');
        $config     = $this->getServiceLocator()->get('config');
        $locales    = $config['translator']['locales'];
        $events         = array();
        $alternatives = array();

        foreach($tableEvents->fetchAll() as $event)
        {
            $events[] = $event;

            if($event->getChainId())
            {
                $alternatives[$event->getChainId()][$event->locale] = $event;
            }
        }

        foreach($events as $event)
        {
            if($event->getChainId() && isset($alternatives[$event->getChainId()]))
            {
                $event->setAlternatives($alternatives[$event->getChainId()]);
            }
        }

        return array(
            'locales'   => $locales,
            'events'    => $events,
        );
    }

    public function addAction()
    {
        $this->layout('layout/admin');
        $config     = $this->getServiceLocator()->get('config');
        $form       = new \Admin\Form\Events();
        $request    = $this->getRequest();
        $tableEvents = $this->getServiceLocator()->get('Pages\Tables\Events');
        $locales    = $config['translator']['locales'];
        $eventsAll  = $tableEvents->fetchAll();

        $form->get('locale')->setValueOptions($config['translator']['locales']);

        $byLocale = array();
        $toSelect = array();

        foreach($eventsAll as $value) {
            $byLocale[$value->locale][$value->url] = $value;
            $toSelect[$value->locale][$value->url] = $value->title;
        }

        foreach($locales as $iso => $title) {
            $form->addChainSelect($iso, $title);

            if(isset($toSelect[$iso])) {
                $form->get($iso)->setValueOptions($toSelect[$iso]);
            }
        }

        if($request->isPost()) {
            $form->setData($request->getPost());

            if($form->isValid()) {
                $event      = new \Pages\ArrayObject\Event();
                $formData   = $form->getData();

                $event->setData($formData);

                $event->added   = time();
                $event->happens = strtotime($event->happens);
                $event->url     = \Application\Library\Stdlib::get_url_string($event->title);

                $tableEvents->add($event);

                $event->id = $tableEvents->getTablegateway()->lastInsertValue;

                $alternatives = array();

                foreach($locales as $iso => $title) {
                    if(
                        isset($formData[$iso]) && 
                        $formData[$iso] && 
                        isset($byLocale[$iso][$formData[$iso]]) && 
                        $iso != $event->locale
                    ) {
                        $alternatives[] = $byLocale[$iso][$formData[$iso]];
                    }
                }

                $this->getServiceLocator()
                    ->get('Indexing\ZendSearch')
                    ->add($event);

                if($alternatives) {
                    $alternatives[] = $event;

                    $this->getServiceLocator()
                        ->get('Pages\AlternativesChain\EventsChain')
                        ->chain($alternatives);
                }

                return $this->redirect()->toRoute(
                    'admin/events', 
                    array(
                        'locale' => $this->params('locale')
                    )
                );
            }
        }

        return array(
            'form'      => $form,
            'locales'   => $locales,
        );
    }

    public function editAction()
    {
        $this->layout('layout/admin');
        $config             = $this->getServiceLocator()->get('config');
        $request            = $this->getRequest();
        $tableEvents        = $this->getServiceLocator()->get('Pages\Tables\Events');

        if(!$event = $tableEvents->getByUrl($this->params('page')))
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form               = new \Admin\Form\Events();
        $locales            = $config['translator']['locales'];
        $eventsAll          = $tableEvents->fetchAll();
        $alternativesChain  = $this->getServiceLocator()->get('Pages\AlternativesChain\EventsChain');
        $errors             = array();

        $form->get('locale')->setValueOptions($config['translator']['locales']);

        $byLocale = array();
        $toSelect = array();

        foreach($eventsAll as $value)
        {
            $byLocale[$value->locale][$value->url] = $value;
            $toSelect[$value->locale][$value->url] = $value->title;
        }

        foreach($locales as $iso => $title)
        {
            $form->addChainSelect($iso, $title);

            if(isset($toSelect[$iso]))
            {
                $form->get($iso)->setValueOptions($toSelect[$iso]);
            }
        }

        if($event->getChainId())
        {
            $chain = $alternativesChain->getChain($event->getChainId());
        }

        if($request->isPost())
        {
            $form->setData($request->getPost());

            if($form->isValid())
            {
                $formData       = $form->getData();
                $oldLocale      = $event->locale;
                $alternatives   = array();
                $wasUnset       = array();

                $event->exchangeArray($formData);
                $event->happens = strtotime($event->happens);

                foreach($locales as $iso => $title)
                {
                    if(isset($formData[$iso]) && $formData[$iso] && isset($byLocale[$iso][$formData[$iso]]) && $iso != $event->locale)
                    {
                        $alternatives[] = $byLocale[$iso][$formData[$iso]];
                    }
                    elseif(isset($chain[$iso]) && (!isset($formData[$iso]) || !$formData[$iso]) && $iso != $oldLocale)
                    {
                        // Alternative was unset
                        $wasUnset[] = $chain[$iso];
                    }
                }

                $indexer = $this->getServiceLocator()->get('Indexing\ZendSearch');
                
                foreach($indexer->find('title:"'.str_replace('"','\"',$event->title).'"') as $hit)
                {
                    if($hit->page == $event->id)
                    {
                        $indexer->delete($hit->id);
                    }
                }           

                $indexer->add($event);

                if($event->getChainId() && $oldLocale == $event->locale)
                {
                    $countChain = count($chain);                
                    $countUnset = count($wasUnset);

                    if($countUnset && $countChain - 1 == $countUnset)
                    {
                        // if all alternatives was unset 
                        // delete only current page from chain
                        $alternativesChain->itemDeleted($event);
                    }
                    if($countUnset && $countChain - 1 > $countUnset)
                    {
                        if($countUnset == 1)
                        {
                            // if one alternative was unset delete it from chain
                            $alternativesChain->itemDeleted(current($wasUnset));
                        }
                        else
                        {
                            $tempIds = array();
                            $tempPages = array();

                            foreach($wasUnset as $item)
                            {
                                $tempIds[] = $item->id;
                            }

                            foreach($tableEvents->getWith(array('id' => $tempIds)) as $item)
                            {
                                $tempPages[] = $event;
                            }

                            if(count($tempPages) > 2)
                            {
                                $alternativesChain->chain($tempPages);
                            }
                        }
                    }
                }
                else
                {
                    if($alternatives)
                    {
                        $alternatives[] = $event;
                        $alternativesChain->chain($alternatives);
                    }
                }

                if($event->getChainId() && $oldLocale != $event->locale)
                {
                    // if locale changed
                    $alternativesChain->itemLocaleChanged($event);
                }

                $tableEvents->edit($event);

                return $this->redirect()->toRoute('admin/events', array('locale' => $this->params('locale')));
            }
        }
        else
        {
            $eventData = $event->getData();

            if(isset($eventData['happens']) && $eventData['happens'])
            {
                if($eventData['happens'] > mktime(0,0,0,1,1,1970))
                {
                    $eventData['happens'] = date('d.m.Y H:i', $eventData['happens']);
                }
            }

            $form->setData($eventData);

            if($event->getChainId())
            {
                $alternatives   = $chain;
                $ids            = array();

                // unset current page from chain
                unset($alternatives[$event->locale]);

                foreach($alternatives as $item)
                {
                    $ids[] = $item->id;
                }

                if($ids)
                {
                    // Get chained elements
                    $alternatives = $tableEvents->getWith(array('id' => $ids));

                    // Set selected alternatives
                    foreach($alternatives as $item)
                    {
                        $form->get($item->locale)->setValue($item->url);
                    }
                }
            }
        }

        return array(
            'form'      => $form,
            'locales'   => $locales,
            'event'     => $event,
            'errors'    => $errors,
        );  
    }

    public function deleteAction()
    {
        $this->layout('layout/admin');
        $request            = $this->getRequest();
        $tableEvents        = $this->getServiceLocator()->get('Pages\Tables\Events');
        $alternativesChain  = $this->getServiceLocator()->get('Pages\AlternativesChain\EventsChain');
        
        if(!($event = $tableEvents->getByUrl($this->params('page'))))
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if($request->getQuery('confirm'))
        {
            $tableEvents->delete($event->id);
            $alternativesChain->itemDeleted($event);

            $indexer = $this->getServiceLocator()->get('Indexing\ZendSearch');

            foreach($indexer->find('title:"'.str_replace('"','\"',$event->title).'"') as $hit)
            {
                if($hit->page == $event->id)
                {
                    $indexer->delete($hit->id);
                }
            }

            if($event instanceof \Pages\AlternativesChain\ChainedItemInterface && $event->getChainId())
            {
                $alternativesChain->itemDeleted($event);
            }

            return $this->redirect()->toRoute('admin/events', array('locale' => $this->params('locale')));
        }

        return array(
            'event' => $event,
        );
    }
}
?>