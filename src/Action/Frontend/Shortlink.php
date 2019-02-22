<?php

namespace Action\Frontend;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Shortlink extends GenericAction {

    /**
     * {@inheritdoc}
     * @return ResponseInterface|callable
     */
    public function __invoke(Request $request, Response $response, $args) {
        /** @var \Domain\Shortlink $shortlink */
        $shortlink = $this->em->getRepository(\Domain\Shortlink::class)
            ->find($args['shortlink']);

        if (empty($shortlink) ||
            !$shortlink->isEnabled()) {
            return $this->notFoundHandler;
        }

        if ($shortlink->isProtected() &&
            $this->session->get('loginStatus') !== true) {
            return $response->withRedirect('/auth/login?callback=/s/' . $shortlink->getId());
        }

        return $this->render($request, $response, 'auth/redirect.html.twig', [
            'href' => $shortlink->getUrl(),
        ]);
    }
}