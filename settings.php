<?php

// settings.php

define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/keys.php';

return [
    'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => false,
        'addContentLengthHeader' => false,

        'doctrine' => [
            // if true, metadata caching is forcefully disabled
            'dev_mode' => true,

            // path where the compiled metadata info will be cached
            // make sure the path exists and it is writable
            'cache_dir' => APP_ROOT . '/var/doctrine',

            // you should add any other path containing annotated entity classes
            'metadata_dirs' => [APP_ROOT . '/src/Domain'],

            'connection' => array_merge([
                'driver'    => 'pdo_mysql',
                'host'      => 'localhost',
                'port'      =>  3306,
                'dbname'    => 'iwgb-cms',
            ], $keys['db']),
        ],
        'twilio' => $keys['twilio'],
        'twig' => [
            'templates_dir' => APP_ROOT .  '/templates/',
//            'cache_dir'     => APP_ROOT . '/cache/',
            'cache_dir'     => false,
            'debug'         => true,
        ],
        'languages' => [
            'available' => ['en', 'es'],
            'fallback'  => 'en',
        ],
    ],
];

