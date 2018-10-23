<?php
namespace Admin\Controller;

use Admin\Form\RegisterUser;
use Users\Identity\User;
use Users\Tables\Control;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use ZfcRbac\Exception\UnauthorizedException;

class UsersController extends AbstractActionController
{
    public function __construct()
    {
    }

    public function onDispatch(MvcEvent $e)
    {
        $this->layout('layout/admin');

        parent::onDispatch($e);
    }

    public function indexAction()
    {
        if (! $this->isGranted('users.manage')) {
            throw new UnauthorizedException();
        }

        $tableControl = $this->getServiceLocator()->get('Users\Tables\Control');
        $config = $this->getServiceLocator()->get('config');
        $users = $tableControl->fetchAll(null, 'id ASC');

        return [
            'users' => $users,
            'config' => $config,
        ];
    }

    public function profileAction()
    {
        if (! $this->identity()) {
            throw new UnauthorizedException();
        }

        /**
         * @var Request $request
         */
        $request = $this->getRequest();

        /**
         * @var Control $tableControl
         */
        $tableControl = $this->getServiceLocator()->get('Users\Tables\Control');

        /**
         * @var RegisterUser $form
         */
        $form = new RegisterUser();

        /**
         * @var User $user
         */
        $user = $this->identity();

        $form->setData($user->getArrayObject()->getData());

        $form->getInputFilter()->get('login')->setRequired(false);
        $form->get('login')->removeAttribute('required')->setAttribute('readonly', 'readonly');
        $form->getInputFilter()->get('password')->setRequired(false);
        $form->get('password')->removeAttribute('required');
        $form->getInputFilter()->get('password-check')->setRequired(false);
        $form->get('password-check')->removeAttribute('required');

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if (isset($formData['password']) && $formData['password']) {
                    $user->getArrayObject()->password = $formData['password'];

                    $user->getArrayObject()->hashPassword();
                }

                $user->getArrayObject()->name = $formData['name'];

                $this->getServiceLocator()
                    ->get('Zend\Authentication\AuthenticationService')
                    ->getStorage()
                    ->write($user->toString());

                $tableControl->edit($user->getArrayObject());

                return $this->redirect()->toRoute(
                    'profile/default',
                    ['locale' => $this->params('locale')]
                );
            }
        }

        return [
            'user' => $user,
            'form' => $form,
        ];
    }

    public function editAction()
    {
        if(!$this->isGranted('users.manage'))
        {
            throw new UnauthorizedException();
        }

        $tableControl   = $this->getServiceLocator()->get('Users\Tables\Control');
        $form           = new RegisterUser();
        $config         = $this->getServiceLocator()->get('config');
        $request        = $this->getRequest();

        if(!$this->params('user') || !($user = $tableControl->getUser($this->params('user'))))
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if($config['super_user'] == $user->roles)
        {
            throw new UnauthorizedException();
        }

        $form->getInputFilter()->get('login')->setRequired(false);
        $form->get('login')->removeAttribute('required')->setAttribute('readonly', 'readonly');
        $form->getInputFilter()->get('password')->setRequired(false);
        $form->get('password')->removeAttribute('required');
        $form->getInputFilter()->get('password-check')->setRequired(false);
        $form->get('password-check')->removeAttribute('required');

        if($user->roles != $config['super_user'])
        {   
            if(!$this->isGranted('users.manage'))
            {
                throw new UnauthorizedException();
            }

            $roles = array_keys($config['zfc_rbac']['role_provider']['ZfcRbac\Role\InMemoryRoleProvider']);
            
            if(false !== ($key = array_search($config['super_user'], $roles)))
            {
                unset($roles[$key]);
            }

            $form->addRolesSelect();
            $form->get('roles')
            ->setValueOptions(array_combine($roles, $roles))
            ->setEmptyOption('Select role');
        }

        $form->setData($user->getArrayObject()->getData());

        if($request->isPost())
        {
            $form->setData($request->getPost());

            if($form->isValid())
            {
                $formData = $form->getData();

                if($user->roles == $config['super_user'] && isset($formData['roles']))
                {
                    unset($formData['roles']);
                }

                if(isset($formData['login']))
                {
                    unset($formData['login']);
                }

                if(isset($formData['password']) && $formData['password'])
                {
                    $user->getArrayObject()->password = $formData['password'];

                    $user->getArrayObject()->hashPassword();

                    unset($formData['password']);
                }

                $user->getArrayObject()->exchangeArray($formData);
                $tableControl->edit($user->getArrayObject());

                return $this->redirect()->toRoute('admin/users', array('locale' => $this->params('locale')));
            }
        }

        return array(
            'form'          => $form,
            'user'          => $user,
            'roleEditable'  => !($user->roles == $config['super_user'])
        );
    }

    public function registerAction()
    {
        $tableControl   = $this->getServiceLocator()->get('Users\Tables\Control');
        $config         = $this->getServiceLocator()->get('config');
        $form           = new RegisterUser();
        $request        = $this->getRequest();
        $usersCount     = $tableControl->getCount();
        $errors         = array();

        if($usersCount)
        {   
            if(!$this->isGranted('users.manage'))
            {
                throw new UnauthorizedException();
            }

            $roles = array_keys($config['zfc_rbac']['role_provider']['ZfcRbac\Role\InMemoryRoleProvider']);

            if(false !== ($key = array_search($config['super_user'], $roles)))
            {
                unset($roles[$key]);
            }

            $form->addRolesSelect();
            $form->get('roles')
            ->setValueOptions(array_combine($roles, $roles))
            ->setEmptyOption('Select role');
        }

        if($request->isPost())
        {
            $form->setData($request->getPost());
            try
            {
                if($form->isValid())
                {
                    $newUser = new \Users\ArrayObject\Control();

                    $newUser->setData($form->getData())->hashPassword();

                    if(!$usersCount)
                    {
                        $newUser->roles = 'Superadmin';
                    }
                        
                    $tableControl->add($newUser);
                    return $this->redirect()->toRoute('admin/users', array('locale' => $this->params('locale')));
                }
            }
            catch(\Exception $e)
            {
                $errors[] = 'User with this login already exists please enter another';
            }
        }

        return array(
            'form'          => $form,
            'usersCount'    => $usersCount,
            'errors'        => $errors,
        );
    }

    public function deleteAction()
    {
        if(!$this->isGranted('users.manage'))
        {
            throw new \ZfcRbac\Exception\UnauthorizedException;
        }

        $tableControl   = $this->getServiceLocator()->get('Users\Tables\Control');
        $form           = new \Admin\Form\RegisterUser();
        $config         = $this->getServiceLocator()->get('config');
        $request        = $this->getRequest();

        if(!$this->params('user') || !($user = $tableControl->getUser($this->params('user'))))
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if($user->roles == $config['super_user'])
        {
            throw new \ZfcRbac\Exception\UnauthorizedException;
        }

        if($this->getRequest()->getQuery('confirm'))
        {
            $tableControl->delete($user->id);

            return $this->redirect()->toRoute('admin/users', array('locale' => $this->params('locale')));
        }

        return array(
            'user' => $user,
        );
    }

    public function logoutAction()
    {
        $this->getServiceLocator()->get('Users\Authentication\AuthenticationService')->clearIdentity();

        return $this->redirect()->toRoute('login/default', array('locale' => $this->params('locale')));
    }
}
