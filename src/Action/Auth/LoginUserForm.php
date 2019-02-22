<?php

namespace Action\Auth;

use Action\GenericAction;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginUserForm extends GenericAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args) {
        return $this->render($request, $response, 'auth/login-official.html.twig', []);
    }
}