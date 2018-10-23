<?php

namespace Pages;

use Zend\Mvc\Router\Http\Segment;

return [
    'controllers' => [
        'invokables' => [
            'Pages\Controller\Page'   => 'Pages\Controller\PageController',
            'Pages\Controller\News'   => 'Pages\Controller\NewsController',
            'Pages\Controller\Events' => 'Pages\Controller\EventsController',
            'Pages\Controller\Blog'   => 'Pages\Controller\BlogController',
            'Pages\Controller\Search' => 'Pages\Controller\SearchController',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../data/languages',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'pages' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/pages',
                    'defaults' => [
                        '__NAMESPACE__' => 'Pages\Controller',
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
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
            ],
            'page' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/page',
                    'defaults' => [
                        '__NAMESPACE__' => 'Pages\Controller',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale[/:page]]',
                            'constraints' => [
                                'locale'    => '[a-z]{2}-[A-Z]{2}',
                                'page'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller'    => 'page',
                                'action'        => 'view',
                            ],
                        ],
                    ],
                ],
            ],
            'categories' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/category',
                    'defaults' => [
                        '__NAMESPACE__' => 'Pages\Controller',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale[/:category[/:year[/:month]]]]',
                            'constraints' => [
                                'locale'    => '[a-z]{2}-[A-Z]{2}',
                                'category'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'year'      => '[0-9]{4}',
                                'month'     => '[0-9]{2}',
                            ],
                            'defaults' => [
                                'controller'    => 'news',
                                'action'        => 'category',
                            ],
                        ],
                    ],
                ],
            ],
            'blog' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/blog',
                    'defaults' => [
                        '__NAMESPACE__' => 'Pages\Controller',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale[/:category]]',
                            'constraints' => [
                                'locale' => '[a-z]{2}-[A-Z]{2}',
                                'category' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'Blog',
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
            'news' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/news',
                    'defaults' => [
                        '__NAMESPACE__' => 'Pages\Controller',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/[:locale[/:category[/:id]]]',
                            'constraints' => [
                                'locale' => '[a-z]{2}-[A-Z]{2}',
                                'category' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => 'News',
                                'action' => 'view',
                            ],
                        ],
                    ],
                    'rss'   => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale]/rss',
                            'constraints' => [
                                'locale'    => '[a-z]{2}-[A-Z]{2}',
                            ],
                            'defaults' => [
                                'controller'    => 'news',
                                'action'        => 'rss',
                            ],
                        ],
                    ],
                ],
            ],
            'events' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/event',
                    'defaults' => [
                        '__NAMESPACE__' => 'Pages\Controller',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale[/:page]]',
                            'constraints' => [
                                'locale'    => '[a-z]{2}-[A-Z]{2}',
                                'page'      => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller'    => 'events',
                                'action'        => 'view',
                            ],
                        ],
                    ],
                    'wall' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale/wall[/:page]]',
                            'constraints' => [
                                'locale'    => '[a-z]{2}-[A-Z]{2}',
                                'page'      => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller'    => 'events',
                                'action'        => 'wall',
                            ],
                        ],
                    ],
                ],
            ],
            'search' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/[:locale]/search[/:page]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Pages\Controller',
                        'controller'    => 'Search',
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __NAMESPACE__ => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewFeedStrategy',
        ],
    ],
];
