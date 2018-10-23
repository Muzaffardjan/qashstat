<?php
return array(
    'controllers' => array(
        'invokables' => array(
            // Controllers
            'UsefulLinks\Controller\Admin' => 'UsefulLinks\Controller\AdminController',
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
    'permissions'   => array(
        'useful_links.manage' => "Useful links control", 
    ),
    'zfc_rbac'      => array(
        'guards' => array(
            'ZfcRbac\Guard\ControllerPermissionsGuard' => array(
                array(
                    'controller'  => 'UsefulLinks\Controller\Admin',
                    'permissions' => array('useful_links.manage'),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin' => array(
            array(
                'label'         => 'Useful links',
                'route'         => 'admin/useful-links',
                'permission'    => 'useful_links.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-link fa-fw',
                'order'         => 99,
                'pages'         => array(
                    array(
                        'label'     => 'Add',
                        'route'     => 'admin/useful-links',
                        'action'    => 'add',
                    ),
                    array(
                        'label'     => 'Edit',
                        'route'     => 'admin/useful-links',
                        'action'    => 'edit',
                    ),
                    array(
                        'label'     => 'Delete',
                        'route'     => 'admin/useful-links',
                        'action'    => 'delete',
                    ),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'useful-links' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale/useful-links[/:action[/:id]]]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'            => '[0-9]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'UsefulLinks\Controller',
                                'controller'    => 'admin',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'UsefulLinks' => __DIR__ . '/../view',
        ),
    ),
    'useful_links' => array(
        'table' => array(
            'name'          => 'useful_links',
            'hydrator_map'  => array(
                // Keys are columns of table
                // Values are property of a ArrayObject
                'id'            => 'id',
                'locale'        => 'locale',
                'url'           => 'url',
                'title'         => 'title',
                'image'         => 'image',
                'order_number'  => 'order',
            ),
        ),
    ),
);
