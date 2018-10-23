<?php
/**
 * Qashqadaryo viloyat statistika boshqarmasi.
 *
 * @author    Muzaffardjan Karaev
 * @copyright Copyright (c) "K-SOFT" LTD 2017-2018
 * @license   "K-SOFT" LTD LICENSE
 * @link      https://karaev.uz
 * Created:   05.01.2018
 */

namespace Admin;

return [
    'controllers' => [
        'invokables' => [
            'Admin\Controller\Categories' => 'Admin\Controller\CategoriesController',
            'Admin\Controller\Index'      => 'Admin\Controller\IndexController',
            'Admin\Controller\Users'      => 'Admin\Controller\UsersController',
            'Admin\Controller\Login'      => 'Admin\Controller\LoginController',
            'Admin\Controller\Pages'      => 'Admin\Controller\PagesController',
            'Admin\Controller\Files'      => 'Admin\Controller\FilesController',
            'Admin\Controller\News'       => 'Admin\Controller\NewsController',
            'Admin\Controller\Events'     => 'Admin\Controller\EventsController',
            'Admin\Controller\Fetch'      => 'Admin\Controller\FetchController',
            'Admin\Controller\Media'      => 'Admin\Controller\MediaController',
            'Admin\Controller\Menu'       => 'Admin\Controller\MenuController',
            'Admin\Controller\Blocks'     => 'Admin\Controller\BlocksController',
            'Admin\Controller\Calendar'   => 'Admin\Controller\CalendarController',
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/admin',
                    'defaults' => [
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale[/:controller[/:action]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [],
                        ],
                    ],
                    'users' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/users[/:action]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'users'
                            ],
                        ],
                    ],
                    'users-as-param' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/users[/:action[/:user]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'user'          => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'users',
                            ],
                        ],
                    ],
                    'pages' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/pages[/:action[/:page]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'page'          => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'pages',
                            ],
                        ],
                    ],
                    'categories' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/categories[/:action[/:category]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'category'      => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => 'categories',
                            ],
                        ],
                    ],
                    'news' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/news[/:action[/:id]]]',
                            'constraints' => [
                                'locale' => '[a-z]{2}-[A-Z]{2}',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => 'news',
                            ],
                        ],
                    ],
                    'events' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/events[/:action[/:page]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'page'          => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'events',
                            ],
                        ],
                    ],
                    'menu' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/menu[/:action[/:menu]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'menu'          => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => 'menu',
                            ],
                        ],
                    ],
                    'media' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/media[/:action[/:collection[/:id]]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'collection'    => '[a-zA-Z0-9]+',
                                'id'            => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'media',
                            ],
                        ],
                    ],
                    'blocks' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/blocks[/:action[/:block[/:target]]]]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'block'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'target'        => '[a-z]{2}-[A-Z]{2}',
                            ],
                            'defaults' => [
                                'controller' => 'blocks',
                            ],
                        ],
                    ],
                ],
            ],
            'login' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Login',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                            ],
                            'defaults' => [
                                'controller'    => 'login',
                                'action'        => 'index',
                            ],
                        ],
                    ],
                ],
            ],
            'profile' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/profile',
                    'defaults' => [
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Users',
                        'action'        => 'profile',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale]',
                            'constraints' => [
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                            ],
                            'defaults' => [
                                'controller'    => 'users',
                                'action'        => 'profile',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'phparray',
                'base_dir' => __DIR__ . '/../data/languages',
                'pattern' => '%s.php',
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __NAMESPACE__ => __DIR__ . '/../view',
        ],
    ],
];
