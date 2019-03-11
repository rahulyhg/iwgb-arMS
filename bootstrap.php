<?php

use Sentry\Event;
use Slim\Container;

define('APP_ROOT', __DIR__);

require APP_ROOT . '/vendor/autoload.php';
spl_autoload_register(function ($class) {
    $file = str_replace(['\\','/'], DIRECTORY_SEPARATOR, '/src/' . $class) . '.php';
    try {
        /** @noinspection PhpIncludeInspection */
        include APP_ROOT . $file;
    } catch (ErrorException $e) {}
});

require_once __DIR__ . '/vendor/autoload.php';

$c = new Container(require __DIR__ . '/settings.php');

\Sentry\init([
    'dsn' => $c['settings']['sentry']['dsn'],
    'release' => JSONObject::get(Config::App, 'app')[0]['version'],
    'before_send' => function (Event $event): Event {

        // suppress warnings from sentry
        if ($event->getLevel() == 'warning') {
            return null;
        }
        return $event;
    },
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
