<?php

namespace Action\Frontend\Tools\Roopal;

use Action\Frontend\GenericMemberAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Form extends GenericMemberAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        return $this->render($request, $response, 'tools/roopal.html.twig', []);
    }
}