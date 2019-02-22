<?php

namespace Action\Auth\User;

use Action\GenericAction;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginForm extends GenericAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args) {
        return $this->render($request, $response, 'auth/login-official.html.twig', []);
    }
}