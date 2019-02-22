<?php

namespace Action\Backend\Settings;

use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class GetJson extends GenericLoggedInAction {

    const CONFIG_DELIMITER = ':';

    /**
     * {@inheritdoc}
     * @return ResponseInterface|callable
     */
    public function __invoke(Request $request, Response $response, array $args) {
        if (in_array($args['config'], \Config::values())) {
            switch ($args['type']) {
                case 'ui':
                    return $response->withJson(\JSONObject::getSchema($args['config'] . '.ui'));
                    break;
                case 'schema':
                    return $response->withJson(\JSONObject::getSchema($args['config']));
                    break;
                case 'config':
                    return $response->withJson(\JSONObject::get($args['config'], $args['item'], true));
                    break;
            }
        }
        return $this->notFoundHandler;
    }


}