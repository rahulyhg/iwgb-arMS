<?php

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthMiddleware {

    const LOGIN_REDIRECT_MESSAGE = 'To view this page, you must log in.';

    private $session;

    private $realm;

    public function __construct(\SlimSession\Helper $session, string $realm) {
        $this->session = $session;
        $this->realm = $realm;
    }

    public function __invoke(Request $request, Response $response, callable $next): ResponseInterface {
        $callback = $request->getUri()->getPath();
        if (($callback != '/auth/login' &&
            !$this->session->get('loginStatus')) ||
            $this->session->get('realm') != $this->realm) {
            return $response->withRedirect('/auth/login?e=' . self::LOGIN_REDIRECT_MESSAGE . "&callback=$callback");
        }

        return $next($request, $response);
    }

}