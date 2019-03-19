<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Csrf;

class FilterableCsrfMiddleware extends Csrf\Guard {

    const CSRF_WHITELIST_BASE_URL = 'no-csrf';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        if (strpos($request->getUri()->getPath(), self::CSRF_WHITELIST_BASE_URL)) {
            return $next($request, $response);
        } else {
            return $this($request, $response, $next);
        }
    }
}