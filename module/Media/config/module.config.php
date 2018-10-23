<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Media\Controller\Media' => 'Media\Controller\MediaController',
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
    'navigation' => array(
        'admin' => array(
            array(
                'label'         => 'Media',
                'route'         => 'admin/media',
                'permission'    => 'media.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-picture-o fa-fw',
                'order'         => 7,
                'pages'         => array(
                    array(
                        'label' => 'Image collections',
                        'route' => 'admin/media',
                        'action'=> 'collections',
                        'pages' => array(
                            array(
                                'label' => 'Add images collection',
                                'route' => 'admin/media',
                                'action'=> 'addImageCollection',
                            ),
                            array(
                                'label' => 'Edit images collection',
                                'route' => 'admin/media',
                                'action'=> 'editImageCollection',
                            ),
                            array(
                                'label' => 'Delete image collection',
                                'route' => 'admin/media',
                                'action'=> 'deleteImageCollection',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Galleries',
                        'route' => 'admin/media',
                        'action'=> 'galleries',
                        'pages' => array(
                            array(
                                'label' => 'Add gallery',
                                'route' => 'admin/media',
                                'action'=> 'addGallery',
                            ),
                            array(
                                'label' => 'Edit gallery',
                                'route' => 'admin/media',
                                'action'=> 'editGallery',
                            ),
                            array(
                                'label' => 'Delete gallery',
                                'route' => 'admin/media',
                                'action'=> 'deleteGallery',
                            ),
                        ), 
                    ),
                    array(
                        'label' => 'Video files',
                        'route' => 'admin/media',
                        'action'=> 'videos',
                        'pages' => array(
                            array(
                                'label' => 'Add video',
                                'route' => 'admin/media',
                                'action'=> 'addVideo',
                            ),
                            array(
                                'label' => 'Edit video',
                                'route' => 'admin/media',
                                'action'=> 'editVideo',
                            ),
                            array(
                                'label' => 'Delete video',
                                'route' => 'admin/media',
                                'action'=> 'deleteVideo',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    // Add module to menu module
    'menu'    => array(
        'modules' => array(
            array(
                'label' => 'Gallery',
                'route' => 'galleries/default',
            ),     
            array(
                'label' => 'Videos',
                'route' => 'videos/default',
            ),       
        ),
    ), 
    'router' => array(
        'routes' => array(
            /*'media' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/media',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Media\Controller',
                        'controller'    => 'Media',
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
                            'route'    => '/[:locale[/:controller[/:action]]]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),*/
            'videos' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/videos',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Media\Controller',
                        'controller'    => 'Media',
                        'action'        => 'videos',
                    ),
                ),
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                            ),
                            'defaults' => array(
                                'controller' => 'Media',
                                'action'     => 'videos',
                            ),
                        ),
                    ),
                ),  
            ),
            'galleries' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/galleries',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Media\Controller',
                        'controller'    => 'Media',
                        'action'        => 'galleries',
                    ),
                ),
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                            ),
                            'defaults' => array(
                                'controller' => 'Media',
                                'action'     => 'galleries',
                            ),
                        ),
                    ),
                ),  
            ),
            'gallery' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/gallery',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Media\Controller',
                        'controller'    => 'Media',
                        'action'        => 'gallery',
                    ),
                ),
                'child_routes' => array(
                    'view' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:locale[/:gallery]]',
                            'constraints' => array(
                                'locale'        => '[a-z]{2}-[A-Z]{2}',
                                'gallery'       => '[a-zA-Z0-9_-]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Media',
                                'action'     => 'gallery',
                            ),
                        ),
                    ),
                ), 
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Media' => __DIR__ . '/../view',
        ),
    ),
    'media' => array(
        'save_path' => __DIR__ . "/../../../public/files/gallery/",
        'public_path' => "/files/gallery/",
        'allowed_extensions' => array(
            'jpg',
            'jpeg',
            'png',
            'gif',
        ),
        'max_file_size' => '2Mb',
        'min_file_size' => '10kb',
        'time_to_live'  => 2 * 86400,   // Two days
    ),
);
