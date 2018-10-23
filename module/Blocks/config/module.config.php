<?php
return [
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../data/languages',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Blocks' => __DIR__ . '/../view',
        ],
    ],
    'blocks' => [
        'eslatma' => [
            'name' => 'Eslatma',
            'description' => 'Eslatmalar uchun',
        ],
        'elonlar' => [
            'name' => 'E\'lonlar',
            'description' => 'E\'lonlar uchun',
        ],
        'tahliliy_malumotlar' => [
            'name' => 'Tahliliy ma\'lumotlar',
            'description' => 'Tahliliy ma\'lumotlar',
        ],
        'stat_books' => [
            'name'        => 'Books',
            'description' => 'Books from stat.uz',
        ],
        'for_businessman' => [
            'name'        => 'Tadbirkorlar uchun',
            'description' => 'Tadbirkorlar uchun',
        ],
        'contact_info' => [
            'name'        => 'Contact info',
            'description' => 'Qayta aloq uchun',
        ],
        'footer_contact_info' => [
            'name'        => 'Contact info',
            'description' => 'Pastga aloq uchun',
        ],
        'hot_phone' => [
            'name' => 'Ishonch raqamlari',
            'description' => 'Ishonch raqamlari',
        ],
		'korrupsiya' => [
            'name' => 'Korrupsiya',
            'description' => 'Korrupsiya',
        ],



        'national_symbols' => [
            'name' => 'Top national symbols',
            'description' => 'Tepadagi davlat ramzlari'
        ],
        
        'footer_center_widget' => [
            'name'          => 'Footer center widget',
            'description'   => 'Center block widget in footer'
        ],
        'reglament' => [
            'name' => 'Footer reglament',
            'description' => 'Footer Reglament'
        ],
        'state_programs' => [
            'name' => 'State programs',
            'description' => 'Photo of state programs'
        ],
        'top_phone' => [
            'name'          => 'Trusted phone on top',
            'description'   => 'Trusted phone number on top block',
        ],
    ],
];
