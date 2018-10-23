<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Quiz\Controller\Index' => 'Quiz\Controller\IndexController',
            'Quiz\Controller\Admin' => 'Quiz\Controller\AdminController',
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
    'permissions' => array(
        'quiz.manage' => "Manage quiz/questions/answers",
    ),
    'zfc_rbac' => array(
        'guards' => array(
            'Users\ZfcRbac\ControllerPermissionsGuard' => array(
                array(
                    'controller'  => 'Quiz\Controller\Admin',
                    'permissions' => array('quiz.manage'),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin' => array(
            array(
                'label'         => 'Quiz',
                'route'         => 'admin/quiz',
                'permission'    => 'quiz.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-question-circle fa-fw',
                'order'         => 10,
                'pages'         => array(
                    array(
                        'label'     => 'Add new question',
                        'route'     => 'admin/quiz',
                        'action'    => 'add',
                    ),
                    array(
                        'label'     => 'Edit question',
                        'route'     => 'admin/quiz',
                        'action'    => 'edit',
                    ),
                    array(
                        'label'     => 'Delete question',
                        'route'     => 'admin/quiz',
                        'action'    => 'delete',
                    ),
                    array(
                        'label'     => 'Add new answer',
                        'route'     => 'admin/quiz',
                        'action'    => 'addAnswer',
                    ),
                    array(
                        'label'     => 'Edit answer',
                        'route'     => 'admin/quiz',
                        'action'    => 'editAnswer',
                    ),
                    array(
                        'label'     => 'Delete answer',
                        'route'     => 'admin/quiz',
                        'action'    => 'deleteAnswer',
                    ),
                    array(
                        'label'     => 'Quiz statistics',
                        'route'     => 'admin/quiz',
                        'action'    => 'statsAll',
                    ),
                ),
            ),
        ),
    ), 
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'quiz' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale/quiz[/:action[/:id]]]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'            => '[0-9]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Quiz\Controller',
                                'controller'    => 'admin',
                            ),
                        ),
                    ),
                ),
            ),
            'quiz' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/quiz',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Quiz\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale[/:controller[/:action[/:id]]]]',
                            'constraints' => array(
                                'locale'            => '[a-z]{2}-[A-Z]{2}',
                                'controller'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'            => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'                => '[0-9]+',
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
            'Quiz' => __DIR__ . '/../view',
        ),
    ),
);
