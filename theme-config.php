<?php
    /* Samples
     * 'checking-version-url' => 'https://www.wisecp.com/samples/themes/version.json',
    */

    return [
        'meta' => [
            'name'          => 'Default',
            'description'   => '',
            'version'       => '1.0',
            'provider'      => 'WISECP',
            'website'       => 'https://www.wisecp.com',
            'image'         => 'cover.jpg',
        ],
        'checking-version-url'  => '',
        'header-types' => [
            1 => [
                'name'  => "Agency",
                'image' => "https://wisecp.com/images/style1.jpg",
            ],
            2 => [
                'name'  => "Corporate",
                'image' => "https://wisecp.com/images/style2.jpg",
            ],
        ],
        'clientArea-types' => [
            1 => [
                'name'  => "WClient",
                'image' => "https://wisecp.com/images/clientArea1.jpg",
            ],
            2 => [
                'name'  => "Basic",
                'image' => "https://wisecp.com/images/clientArea2.jpg",
            ]
        ],
        'settings'                 => [
            'header-type'          => 1,
            'clientArea-type'      => 1,
            'color1'               => '009595',
            'color2'               => '345a6c',
            'text-color'           => '607d8b',
            'meta-color'           => '#009595',
        ],
    ];