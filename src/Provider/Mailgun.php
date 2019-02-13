<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Mailgun\Mailgun as MailgunClient;

class Mailgun implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Container $c) {
        $c['mailgun'] = MailgunClient::create('key-' . $c['settings']['mailgun']['key'],
            'https://api.eu.mailgun.net');
    }
}
