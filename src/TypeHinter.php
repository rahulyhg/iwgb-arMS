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

    /** @var  $csrf \Slim\Csrf\Guard */
    public $csrf;

    /** @var $session \SlimSession\Helper */
    public $session;

    /** @var $http \Buzz\Browser */
    public $http;

    /** @var $mailgun \Mailgun\Mailgun */
    public $mailgun;

    /** @var $settings array */
    public $settings;

    /** @var $email \Emailer */
    public $email;

    /** @var $send \Sender */
    public $send;

    /** @var $recaptcha \Recaptcha\Recaptcha */
    public $recaptcha;

    /** @var $cdn \Aws\S3\S3Client */
    public $cdn;
}