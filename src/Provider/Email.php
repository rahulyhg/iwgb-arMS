<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Email implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Container $c) {
        /** @var $c \TypeHinter */
        $c['email'] = new \Emailer($c->mailgun,
            $c->view,
            $c->settings['mailgun']['domain'],
            $c->settings['mailgun']['from'],
            $c->settings['mailgun']['replyTo']);
    }
}