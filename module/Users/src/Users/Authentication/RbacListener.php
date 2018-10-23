<?php
namespace Users\Authentication;
 
use Zend\EventManager\EventInterface;
use Zend\Navigation\Page\AbstractPage;
use ZfcRbac\Service\AuthorizationServiceInterface;
 
class RbacListener
{
    /**
     * @var AuthorizationServiceInterface
     */
    protected $authorizationService;
 
    /**
     * @param AuthorizationServiceInterface $authorizationService
     */
    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }
 
    /**
     * @param  EventInterface $event
     * @return bool|void
     */
    public function accept(EventInterface $event)
    {
        $page = $event->getParam('page');
 
        if (!$page instanceof AbstractPage) {
            return;
        }
 
        $permission = $page->getPermission();
 
        if (null === $permission) {
            return;
        }
 
        $event->stopPropagation();

        if($this->authorizationService->isGranted($permission))
        {
            return true;
        }
        else
        {
            $page->setVisible(false);

            if($page->getParent())
            {
                $page->getParent()->removePage($page);
            }
            
            return false;
        }
    }
}