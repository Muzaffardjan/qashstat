<?php  
return array(
	'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Navigation\Service\NavigationAbstractServiceFactory'
        ),
    ),
    'navigation' => array(
        'admin' => array(
            array(
                'label'         => 'Dashboard',
                'route'         => 'admin/default',
                'navigationOnly'=> true, 
                'icon'          => 'fa fa-pie-chart fa-fw',
                'permission'    => 'dashboard.access',
                'order'         => -1,
            ),
            array(
                'label'         => 'Users management',
                'route'         => 'admin/users-as-param',
                'permission'    => 'users.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-slideshare fa-fw',
                'order'         => 1,
                'pages'         => array(
                    array(
                        'label' => 'Edit user',
                        'route' => 'admin/users-as-param',
                        'action'=> 'edit',
                    ),
                    array(
                        'label' => 'Register user',
                        'route' => 'admin/users-as-param',
                        'action'=> 'register',
                    ),
                    array(
                        'label' => 'Delete user',
                        'route' => 'admin/users-as-param',
                        'action'=> 'delete',
                    ),
                ),
            ),
            array(
                'label'         => 'Pages management',
                'route'         => 'admin/pages',
                'permission'    => 'pages.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-clipboard fa-fw',
                'order'         => 2,
                'pages'         => array(
                    array(
                        'label' => 'Add new page',
                        'route' => 'admin/pages',
                        'action'=> 'add',
                    ),
                    array(
                        'label' => 'Edit page',
                        'route' => 'admin/pages',
                        'action'=> 'edit',
                    ),
                    array(
                        'label' => 'Delete page',
                        'route' => 'admin/pages',
                        'action'=> 'delete',
                    ),
                ),
            ),
            array(
                'label'         => 'Categories',
                'route'         => 'admin/categories',
                'permission'    => 'categories.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-list fa-fw',
                'order'         => 3,
                'pages'         => array(
                    array(
                        'label' => 'Add category',
                        'route' => 'admin/categories',
                        'action'=> 'add',
                    ),
                    array(
                        'label' => 'Edit category',
                        'route' => 'admin/categories',
                        'action'=> 'edit',
                    ),
                    array(
                        'label' => 'Delete category',
                        'route' => 'admin/categories',
                        'action'=> 'delete',
                    ),
                ),
            ),
            array(
                'label'         => 'News',
                'route'         => 'admin/news',
                'permission'    => 'news.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-newspaper-o fa-fw',
                'order'         => 4,
                'pages'         => array(
                    array(
                        'label' => 'Add news',
                        'route' => 'admin/news',
                        'action'=> 'add',
                    ),
                    array(
                        'label' => 'Edit news',
                        'route' => 'admin/news',
                        'action'=> 'edit',
                    ),
                    array(
                        'label' => 'Delete news',
                        'route' => 'admin/news',
                        'action'=> 'delete',
                    ),
                ),
            ),
            array(
                'label'         => 'Events',
                'route'         => 'admin/events',
                'permission'    => 'events.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-calendar-o fa-fw',
                'order'         => 5,
                'pages'         => array(
                    array(
                        'label' => 'Add event',
                        'route' => 'admin/events',
                        'action'=> 'add',
                    ),
                    array(
                        'label' => 'Edit event',
                        'route' => 'admin/events',
                        'action'=> 'edit',
                    ),
                    array(
                        'label' => 'Delete event',
                        'route' => 'admin/events',
                        'action'=> 'delete',
                    ),
                ),
            ),
            array(
                'label'         => 'Menu manager',
                'route'         => 'admin/menu',
                'permission'    => 'menu.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-sitemap fa-fw',
                'order'         => 6,
                'pages'         => array(
                    array(
                        'label'     => 'Add new menu',
                        'route'     => 'admin/menu',
                        'action'    => 'add',
                    ),
                ),
            ),
            array(
                'label'         => 'Blocks',
                'route'         => 'admin/blocks',
                'permission'    => 'blocks.manage',
                'action'        => 'index',
                'single'        => true,
                'icon'          => 'fa fa-cubes fa-fw',
                'order'         => 8,
                'pages'         => array(
                    array(
                        'label'     => 'Edit block',
                        'route'     => 'admin/blocks',
                        'action'    => 'add',
                    ),
                ),
            ),
            array(
                'label'             => 'Profile',
                'route'             => 'profile/default',
                'breadcrumbsOnly'   => true,
            ),
        ),
    ),
);
?>