<?php

namespace Action\Auth;

use Action\Backend\GenericLoggedInAction;
use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Logout extends GenericAction {

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $this->session->clear();
        return $response->withRedirect('/auth/login?m=You have been logged out.');
    }
}