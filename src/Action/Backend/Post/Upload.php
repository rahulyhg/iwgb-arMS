<?php

namespace Action\Backend\Post;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Upload extends GenericAction {

    // legacy bodge

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        $this->cdn->putObject([
            'Bucket' => $this->settings['spaces']['bucket'],
            'Key' => 'post/' . $args['name'],
            'ACL' => 'public-read',
            'ContentType' => $args['type'] . '/' . $args['ext'],
            'SourceFile' => APP_ROOT . '/var/upload/' . $args['name'],
        ]);

        unlink(APP_ROOT . '/var/upload/' . $args['name']);

        return $response->withJson(['status' => 'success']);


    }
}