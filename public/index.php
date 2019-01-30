<?php

use Slim\App;
use Slim\Container;

spl_autoload_register(function ($className) {
    /** @noinspection PhpIncludeInspection */
    include __DIR__ . '/../src/' . $className . '.php';
});

/** @var Container $container */
$container = require_once __DIR__ . '/../bootstrap.php';

/** @var App $app */
$app = $container['slim'];

/** @noinspection PhpUnhandledExceptionInspection */
$app->run();