<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericPublicAction;

class ChooseBranch extends GenericPublicAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
        return $this->render($request, $response, 'join/join.html.twig', [
            'joinCopy'  => \JSONObject::getItem(\Config::Pages, 'join'),
            'branches'  => \JSONObject::getAllItems(\Config::Branches),
        ]);
    }
}