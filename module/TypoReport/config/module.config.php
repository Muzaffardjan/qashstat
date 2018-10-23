<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'TypoReport\Controller\Report'  => 'TypoReport\Controller\ReportController',
            'TypoReport\Controller\Admin'   => 'TypoReport\Controller\AdminController',
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
        'typo_report.manage' => "TypoReport management",
    ),
    'zfc_rbac' => array(
        'guards' => array(
            'Users\ZfcRbac\ControllerPermissionsGuard' => array(
                array(
                    'controller'  => 'TypoReport\Controller\Admin',
                    'permissions' => array('typo_report.manage'),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin' => array(
            array(
                'label'         => 'Typo',
                'route'         => 'admin/typo-report',
                'permission'    => 'typo_report.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-strikethrough fa-fw',
                'order'         => 11,
                'pages'         => array(
                ),
            ),
        ),
    ), 
    'router' => array(
        'routes' => array(
            'admin'       => array(
                'child_routes' => array(
                    'typo-report' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale/typo-report[/:action[/:id]]]',
                            'constraints' => array(
                                'locale'     => '[a-z]{2}-[A-Z]{2}',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]+',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'TypoReport\Controller',
                                'controller'    => 'admin',
                            ),
                        ),
                    ),
                ),
            ),
            'typo-report' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/report-typo',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'TypoReport\Controller',
                        'controller'    => 'Report'
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
                            'route'    => '/[:locale[/:controller[/:action]]]',
                            'constraints' => array(
                                'locale'     => '[a-z]{2}-[A-Z]{2}',
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
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
            'TypoReport' => __DIR__ . '/../view',
        ),
    ),
);
