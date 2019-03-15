<?php

namespace Action\Backend;

use Action\GenericAction;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

class GetCode extends GenericAction {


    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        $headers = $request->getHeader('X-Pull');
        if ($headers &&
            $headers[0] != $this->settings['cdn']['pullKey']) {
            return $response->withStatus(403);
        }

        try {
            $file = fopen(APP_ROOT . '/' . $args['folder'] . '/' . $args['file'], 'r');
        } catch (Exception $e) {
            return $response->withStatus(404);
        }

        $mime = '';
        switch ($args['folder']) {
            case 'css':
                $mime = 'text/css';
                break;
            case 'js':
                $mime = 'application/javascript';
                break;
        }

        $response = $response->withHeader('Content-Type', $mime);
        return $response->withBody(new Stream($file));
    }
}