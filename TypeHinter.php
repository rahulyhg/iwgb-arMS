<?php

class TypeHinter {

    /** @var $view Slim\Views\Twig */
    public $view;

    /** @var $notFound callable */
    public $notFoundHandler;

    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;

    /** @var $slim \Slim\App */
    public $slim;

    /** @var $twilio \Twilio\Rest\Client */
    public $twilio;
}