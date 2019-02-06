<?php

use Slim\Container;

require_once __DIR__ . '/vendor/autoload.php';

$c = new Container(require __DIR__ . '/settings.php');

$c->register(new Provider\Doctrine())
    ->register(new Provider\Slim())
    ->register(new Provider\Twig())
    ->register(new Provider\HttpClient())
    ->register(new Provider\Twilio());

return $c;
