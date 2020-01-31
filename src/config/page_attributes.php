<?php

return [

    'class' => 'Fsmdev\LaravelPageAttributes\Models\PageAttributes',

    'multi_language' => env('FSMDEV_MULTI_LANGUAGE', false),

    'default' => [
        'charset' => 'utf-8',
        'viewport' => 'width=device-width, initial-scale=1',
    ],

    'html_templates' => [
    ],

    'default_variables' => [
    ],

    'variable_open' => '{--',
    'variable_close' => '--}',
];