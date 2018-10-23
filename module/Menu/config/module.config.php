<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Menu\Controller\Sitemap'      => 'Menu\Controller\SitemapController',
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
            'sitemap' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/[:locale]/sitemap',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Menu\Controller',
                        'controller'    => 'Sitemap',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'menu' => __DIR__ . '/../view',
        ),
    ),
    'menu' => array(
        'positions' => array(
            'statictop' => array(
                'name'              => 'Static top',
                'maxDepth'          => 4,
                'maxRootCount'      => 8,
                'maxChildsCount'    => 8,
            ),
        ),
    ),
);
