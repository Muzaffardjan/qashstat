<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Newsletter\Controller\Index'   => 'Newsletter\Controller\IndexController',
            'Newsletter\Controller\Manage'  => 'Newsletter\Controller\ManageController',
            'Newsletter\Controller\Admin'   => 'Newsletter\Controller\AdminController'
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
                    'newsletter' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale/newsletter[/:action[/:link]]]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'link'          => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Newsletter\Controller',
                                'controller'    => 'admin',
                            ),
                        ),
                    ),
                ),
            ),
            'newsletter' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/newsletter',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Newsletter\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale[/:controller[/:action[/:link]]]]',
                            'constraints' => array(
                                'locale'            => '[a-z]{2}-[A-Z]{2}',
                                'controller'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'            => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'link'              => '[a-zA-Z0-9_-]*',
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
    'newsletter' => array(
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
    'view_manager' => array(
        'template_path_stack' => array(
            'Newsletter' => __DIR__ . '/../view',
        ),
    ),
);
