<?php

namespace Action\Backend\Media;

use Action\Backend\GenericLoggedInAction;
use Aws\S3\S3Client;
use Slim\Container;

abstract class GenericSpacesAction extends GenericLoggedInAction {

    protected $cdn;

    protected $bucket;

    protected $root;

    const DEFAULT_PATH = 'bucket/';

    public function __construct(Container $c) {
        parent::__construct($c);
        $this->cdn = new S3Client([
           'version'    => 'latest',
           'region'     => $this->settings['spaces']['region'],
           'endpoint'   => 'https://' . $this->settings['spaces']['region'] . '.digitaloceanspaces.com',
           'credentials'=> $this->settings['spaces']['credentials'],
        ]);

        $this->bucket = $this->settings['spaces']['bucket'];
        $this->root = base64_encode(self::DEFAULT_PATH);
    }
}