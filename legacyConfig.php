<?php

$keys = require APP_ROOT . '/legacyKeys.php';

return [
    'displayErrorDetails'       => true,
    'addContentLengthHeader'    => false,
    'db'        => [
        'host'  => $c['settings']['doctrine']['connection']['host'],
        'user'  => $c['settings']['doctrine']['connection']['user'],
        'pass'  => $c['settings']['doctrine']['connection']['password'],
        'dbname'=> $c['settings']['doctrine']['connection']['dbname'],
    ],
    'twilio'    => $c['settings']['twilio'],
    'mailgun'   => [
        'key' => $keys['mailgun']['key'],
    ],
    'iwgb' => $keys['iwgb'],
];