<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericPublicAction;
use Slim\Http\Request;
use Slim\Http\Response;

class Form extends GenericPublicAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args) {
        $branch = \JSONObject::get(\Config::Branches, $args['branch']);
        if ($branch === false) {
            return $this->notFoundHandler;
        }

        return $this->render($request, $response, 'join/form.html.twig', [
            'branch'        => $branch,
            'join'          => \JSONObject::get(\Config::Forms, 'join'),
            'recaptchaSite' => $this->settings['recaptcha']['site'],
        ]);
    }
}