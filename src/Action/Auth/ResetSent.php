<?php

namespace Action\Auth;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ResetSent extends GenericAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        return $this->render($request, $response, 'auth/reset-sent.html.twig', []);
    }
}