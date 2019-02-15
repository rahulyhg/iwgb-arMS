<?php

namespace Action\Auth;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericAuthAction extends GenericAction {

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, Response $response, string $template, $vars): ResponseInterface {
        return parent::render($request,$response, $template, $vars);
    }

}