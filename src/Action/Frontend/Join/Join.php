<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericPublicAction;
use Psr\Http\Message\ResponseInterface;

class Join extends GenericPublicAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, $args): ResponseInterface {
        return $this->render($request, $response, 'join/join.html.twig', [
            'joinCopy'  => \JSONObject::get(\Config::Pages, 'join'),
            'branches'  => \JSONObject::getAll(\Config::Branches),
        ]);
    }
}