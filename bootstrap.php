<?php

use Sentry\Event;
use Slim\Container;

define('APP_ROOT', __DIR__);

error_reporting(E_ALL ^ E_WARNING);

require APP_ROOT . '/vendor/autoload.php';
spl_autoload_register(function ($class) {
    $file = str_replace(['\\','/'], DIRECTORY_SEPARATOR, '/src/' . $class) . '.php';
    /** @noinspection PhpIncludeInspection */
    include APP_ROOT . $file;
});

require_once __DIR__ . '/vendor/autoload.php';

$c = new Container(require __DIR__ . '/settings.php');

\Sentry\init([
    'dsn' => $c['settings']['sentry']['dsn'],
]);

$c->register(new Provider\Doctrine())
    ->register(new Provider\Slim())
    ->register(new Provider\Twig())
    ->register(new Provider\HttpClient())
    ->register(new Provider\Twilio())
    ->register(new Provider\Mailgun())
    ->register(new Provider\Send())
    ->register(new Provider\Cdn());

return $c;
