<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Send implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Container $c) {
        /** @var $c \TypeHinter */

        $c['send'] = new \Sender(
            new \Emailer($c->mailgun,
                $c->view,
                $c->settings['mailgun']['domain'],
                $c->settings['mailgun']['from'],
                $c->settings['mailgun']['replyTo']),
            $c->mailgun,
            $c->twilio,
            $c->settings['twilio']);
    }
}