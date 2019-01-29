<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Twilio implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Container $c) {

        $c['twilio'] = function (Container $c): \Twilio\Rest\Client {
            return new \Twilio\Rest\Client($c['settings']['twilio']['sid'], $c['settings']['twilio']['token']);
        };
    }
}