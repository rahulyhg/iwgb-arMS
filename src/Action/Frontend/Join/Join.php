<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericPublicAction;

class Join extends GenericPublicAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
        return $this->render($request, $response, 'join/join.html.twig', [
            'joinCopy'  => (array) new JSONObject(\Config::Pages, 'join'),
            'branches'  => (array) JSONObject::getAllItems(\Config::Branches),
        ]);
    }
}