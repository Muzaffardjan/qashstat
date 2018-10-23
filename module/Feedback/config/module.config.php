<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Feedback\Controller\Index' => 'Feedback\Controller\IndexController',
            'Feedback\Controller\Admin' => 'Feedback\Controller\AdminController',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../data/languages',
                'pattern'  => '%s.php',
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'feedback' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale/feedback[/:action[/:id]]]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'            => '[0-9]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Feedback\Controller',
                                'controller'    => 'admin',
                            ),
                        ),
                    ),
                ),
            ),
            'feedback' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/feedback',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Feedback\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale[/:controller[/:action]]]',
                            'constraints' => array(
                                'locale'            => '[a-z]{2}-[A-Z]{2}',
                                'controller'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'            => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'Index',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Feedback' => __DIR__ . '/../view',
        ),
    ),
    'zfc_rbac'      => array(
        'guards' => array(
            'ZfcRbac\Guard\ControllerPermissionsGuard' => array(
                array(
                    'controller'  => 'Feedback\Controller\Admin',
                    'permissions' => array('contacts.manage'),
                ),
            ),
        ),
    ),
    'permissions'   => array(
        'contacts.manage' => "Contacts control", 
    ),
    'navigation' => array(
        'admin' => array(
            array(
                'label'         => 'Feedback',
                'route'         => 'admin/feedback',
                'permission'    => 'contacts.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-reply fa-fw',
                'order'         => 100,
                'pages'         => array(
                    array(
                        'label'     => 'Read',
                        'route'     => 'admin/feedback',
                        'action'    => 'read',
                    ),
                ),
            ),
        ),
    ),
    /*'di' => array(
        'class' => array(
            ''
        ),
    ),*/
    'feedback' => array(
        'sender' => array(
            'from' => 'dev@each.uz',
            'transport' => array(
                'name'          => 'Zend\Mail\Transport\Smtp',
                'options_class' => 'Zend\Mail\Transport\SmtpOptions',
                'options'       => array(
                    'name'              => 'mail.ahost.uz',
                    'host'              => 'mail.ahost.uz',
                    'connection_class'  => 'login',
                    'connection_config' => array(
                        'username' => 'dev@each.uz',
                        'password' => 'Java2277224',
                        //'ssl'      => 'tls',
                        'port'     => 465,
                    ),
                ),
            ),
        ),
    ),
);
