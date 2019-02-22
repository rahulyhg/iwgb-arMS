<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericPublicAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Form extends GenericPublicAction {

    /**
     * {@inheritdoc}
     * @return ResponseInterface|callable
     */
    public function __invoke(Request $request, Response $response, array $args) {
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