<?php

namespace Provider;

use Aws\S3\S3Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Cdn implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Container $c) {
        /** @var $c \TypeHinter */

        $c['cdn'] = new S3Client([
            'version'    => 'latest',
            'region'     => $c->settings['spaces']['region'],
            'endpoint'   => 'https://' . $c->settings['spaces']['region'] . '.digitaloceanspaces.com',
            'credentials'=> $c->settings['spaces']['credentials'],
        ]);
    }
}