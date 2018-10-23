<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index'     => 'Application\Controller\IndexController',
            'Application\Controller\JsPlugins' => 'Application\Controller\JsPluginsController',
            'Application\Controller\Time'      => 'Application\Controller\TimeController',
            'Application\Controller\Narrator'  => 'Application\Controller\NarratorController',
            'Application\Controller\Weather'   => 'Application\Controller\WeatherController',
            'Application\Controller\Calendar'  => 'Application\Controller\CalendarController',
        ],
    ],
    'router' => [
        'routes' => [
        	'calendar' => [
        		'type' => Literal::class,
        		'options' => [
        			'route' => '/calendar',
        			'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Calendar',
                        'action'        => 'index'
        			],
        		],
                'may_terminate' => true,
                'child_routes' => [
                	'view' => [
                        'type' => Segment::class,
                        'options' => [
                        	'route' => '/view[/:id]',
                        	'constraints' => [
                        		'id' => '[0-9]',
                        	],
                        	'defaults' => [
		                        '__NAMESPACE__' => 'Application\Controller',
		                        'controller'    => 'Calendar',
		                        'action'        => 'view'
                        	],
                        ],
                	],
                ],
        	],
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'redirect',
                    ],
                ],
            ],
            'home-locale' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '[/:locale]',
                    'constraints' => ['locale' => '[a-z]{2}-[A-Z]{2}'],
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
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
                                'locale'     => '[a-z]{2}-[A-Z]{2}',
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [],
                        ],
                    ],
                ],
            ],
            'weather' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/weather',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Weather',
                        'action'        => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'cities' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/cities[/:city[/:locale]]',
                            'constraints' => [
                                'city'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'locale' => '[a-z]{2}-[A-Z]{2}',
                            ],
                            'defaults' => [
                                'controller' => 'Weather',
                                'action' => 'cities',
                            ],
                        ],
                    ],
                    'getall' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/getall[/:locale]',
                            'constraints' => [
                                'locale' => '[a-z]{2}-[A-Z]{2}',
                            ],
                            'defaults' => [
                                'controller' => 'Weather',
                                'action' => 'getall',
                            ],
                        ],
                    ],
                ],
            ],
            'time' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/time',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Time',
                        'action'        => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:locale]',
                            'constraints' => [
                                'locale' => '[a-z]{2}-[A-Z]{2}',
                            ],
                            'defaults' => [
                                'controller' => 'Time',
                                'action'     => 'index'
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
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
    'weather' => [
        'dir' => __DIR__ . '/../../../public/weather',
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [__DIR__ . '/../view'],
    ],
    'console' => ['router' => ['routes' => []]],
    'narrator' => [
        'client_options' => [
            'maxredirects' => 5,
            'timeout'      => 10
        ],
        'voice_messages' => [
            'directory'     => __DIR__ . '/../voice_messages/',
            'name_template' => 'error_%s.vmsg',
        ],
        'credentials' => [
            'accessKey' => 'GDNAJWLYTNOWKYNLWR6Q',
            'secretKey' => 'Z/7BWYDsU5p/L+Aq1/2C8oYE3ZR/NdBfs17HHdOF',
        ],
        'request_method' => 'POST',
        'canonical_uri' => '/CreateSpeech',
        'region' => 'eu-west-1',
        'host' => 'tts.eu-west-1.ivonacloud.com',
    ],
];
