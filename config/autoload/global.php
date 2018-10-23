<?php
use ZfcRbac\Guard\GuardInterface;

return [
    'view_manager' => ['strategies' => ['ViewJsonStrategy']],
    'translator' => [
        'locales' => [
            'uz-UZ' => "O'zbek",
            /*'en-US' => "English",
            'ru-RU' => "Русский",*/
        ],
        'locale'    => 'uz-UZ',
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => 'data/languages/framework',
                'pattern'  => '%s.php',
            ],
            [
                'type'     => 'phparray',
                'base_dir' => 'data/languages/application',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'super_user' => 'Superadmin',
    'zfc_rbac' => [
        'redirect_strategy' => [
            'redirect_when_connected'        => false,
            'redirect_to_route_disconnected' => 'home',
            'append_previous_uri'            => true,
            'previous_uri_query_key'         => 'redirectTo',
        ],
        'role_provider' => [
            'ZfcRbac\Role\InMemoryRoleProvider' => [
                'Superadmin' => [],
                'Admin' => [
                    'permissions' => [
                        'dashboard.access',
                        'pages.manage',
                    ],
                ],
            ],
        ],
        'guard_manager' => [
            'factories' => [
                'Users\ZfcRbac\ControllerPermissionsGuard' => 'Users\ZfcRbac\Factory\ControllerPermissionsGuardFactory'
            ],
        ],
        'guards' => [
            'Users\ZfcRbac\ControllerPermissionsGuard' => [
                [
                    'controller'  => 'Admin\Controller\Index',
                    'permissions' => ['dashboard.access'],
                ],
                [
                    'controller'  => 'Admin\Controller\Pages',
                    'permissions' => ['pages.manage'],
                ],
                [
                    'controller'  => 'Admin\Controller\Files',
                    'permissions' => ['pages.files'],
                ],
                [
                    'controller'  => 'Admin\Controller\News',
                    'permissions' => ['news.manage'],
                ],
                [
                    'controller'  => 'Admin\Controller\Categories',
                    'permissions' => ['categories.manage'],
                ],
                [
                    'controller'  => 'Admin\Controller\Events',
                    'permissions' => ['events.manage'],
                ],
                [
                    'controller'  => 'Admin\Controller\Fetch',
                    'permissions' => ['fetch.bysearch'],
                ],
                [
                    'controller'  => 'Admin\Controller\Media',
                    'permissions' => ['media.manage'],
                ],
            ],
        ],
    ],
    'route_aliases' => [
        'admin.*' => 'login/default',
    ],
    'files_explorer' => [
        'root' => 'public/files/',
        'validators' => [
            'extension' => [
                'denied' => [
                    'php',
                    'phtml',
                    'html',
                    'htaccess',
                ],
            ],
        ],
    ],
    'pages' => [
        'files_upload' => [
            'public'    => 'files/uploads/',
            'private'   => 'public/files/uploads/',
        ],
    ],
];