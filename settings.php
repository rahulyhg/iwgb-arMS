<?php

// settings.php

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
        'twilio' => [
            'sid'   => 'AC6bcc5bc5cdadd1818a7c2129684a2630',
            'token' => $keys['twilio'],
            'from'  => 'IWGB',
        ],
        'twig' => [
            'templates_dir' => APP_ROOT .  '/view/',
//            'cache_dir'     => APP_ROOT . '/var/twig',
            'cache_dir'     => false,
            'debug'         => true,
        ],
        'languages' => [
            'available' => ['en', 'es'],
            'fallback'  => 'en',
        ],
        'recaptcha' => [
            'site'  => '6LeqJU0UAAAAAOdkuqvkHwfzEz_yR9igu7NpSEKn',
            'secret'=> $keys['recaptcha'],
        ],
        'mailgun' => [
            'domain'    => 'mx.iwgb.org.uk',
            'key'       => $keys['mailgun'],
            'from'      => 'noreply@iwgb.org.uk',
            'replyTo'   => 'office@iwgb.org.uk',
        ],
        'cdn'   => [
            'baseUrl' => 'cdn.iwgb.org.uk',
        ],
        'sso' => [
            'signature' => $keys['sso'],
        ],
        'spaces' => [
            'credentials' => [
                'key'   => 'KOWTSWXXMKRJFEXMSIGK',
                'secret'=> $keys['spaces'],
            ],
            'region' => 'ams3',
            'bucket' => 'iwgb',
        ]
    ],
];

