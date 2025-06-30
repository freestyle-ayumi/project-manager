<?php

return [

    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,


    'font_dir' => storage_path('fonts/'),
    'font_cache' => storage_path('fonts/'),
    'default_font' => 'ipaexg',

    'fonts' => [
        'ipaexg' => [
            'R' => 'ipaexg.ttf',
            'B' => 'ipaexg.ttf',
            'I' => 'ipaexg.ttf',
            'BI' => 'ipaexg.ttf',
        ],
    ],


    // ✅ そのほかのオプションは options 配下でOK
    'options' => [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => false,
        'dpi' => 96,
        'defaultMediaType' => 'screen',
        'defaultPaperSize' => 'a4',
        'defaultPaperOrientation' => 'portrait',
        'font_height_ratio' => 1.1,
        'enable_javascript' => true,
        'enable_php' => false,
        'enable_remote' => false,
        'allowed_remote_hosts' => null,
        'chroot' => realpath(base_path()),
    ],

];
