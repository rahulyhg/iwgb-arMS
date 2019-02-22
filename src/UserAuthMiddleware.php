<?php

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UserAuthMiddleware {

    const LOGIN_REDIRECT_MESSAGE = 'To view this page, you must log in.';

    private $session;

    public function __construct(\SlimSession\Helper $session) {

        /** @var $c \TypeHinter */
        $this->session = $session;
    }

    public function __invoke(Request $request, Response $response, callable $next): ResponseInterface {
        $callback = $request->getUri()->getPath();
        if (($callback != '/arms/login' &&
            !$this->session->get('loginStatus')) ||
            $this->session->get('realm') != 'official') {
            return $response->withRedirect('/auth/login?e=' . self::LOGIN_REDIRECT_MESSAGE . "&callback=$callback");
        }

        return $next($request, $response);
    }

}