<?php

namespace Action\Auth\User;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class SendResetForm extends GenericAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        return $this->render($request, $response, 'auth/send-reset.html.twig', []);
    }
}