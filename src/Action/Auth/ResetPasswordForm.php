<?php

namespace Action\Auth;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ResetPasswordForm extends GenericAction {

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        /** @var \Domain\Event $event */
        $event = $this->em->getRepository(\Domain\Event::class)->find($args['id']);
        $secret = $request->getQueryParam('secret');

        if (empty($event) ||
            empty($secret) ||
            $secret != $event->getData() ||
            $event->getTimestamp() < new \DateTime('-15 minutes')) {
            return $response->withRedirect('/auth/invalid');
        }

        $user = $this->em->getRepository(\Domain\User::class)->find($event->getWho());

        return $this->render($request, $response, 'auth/reset-form.html.twig', [
            'user'  => $user,
            'event' => $event,
        ]);
    }
}