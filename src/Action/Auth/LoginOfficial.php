<?php

namespace Action\Auth;

use Action\GenericAction;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginOfficial extends GenericAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args) {
        return $this->render($request, $response, 'auth/login-official.html.twig', []);
    }
}