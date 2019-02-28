<?php

namespace Action\Backend\Media;

use Action\Backend\GenericLoggedInAction;
use Aws\S3\S3Client;
use Slim\Container;
use Twig_Function;

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

        $this->view->getEnvironment()->addFunction(new Twig_Function('isImage', function($s) {
            return self::isImage($s);
        }));
    }

    protected static function getFileType($file) {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    protected static function isImage($file) {
        return in_array(self::getFileType($file), ['jpg', 'png']);
    }
}