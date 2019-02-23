<?php

namespace Action\Auth\SSO;

use Action\Frontend\GenericMemberAction;
use Action\GenericAction;
use Domain\Event;
use Slim\Http\Request;
use Slim\Http\Response;

class Redirect extends GenericMemberAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args) {

        /** @var Event $event */
        $event = $this->em->getRepository(Event::class)
            ->find($args['id']);

        if (empty($event) ||
            !$this->session->get('loginStatus')) {
            return $response->withRedirect('/auth/invalid');
        }

        $link = '';
        switch ($event->getWho()) {
            case Clients::Discourse:
                $link = 'https://discuss.clb.iwgb.org.uk/session/sso_login';
                $payload = base64_encode(http_build_query([
                    'nonce'         => $event->getData(),
                    'email'         => $this->member->getEmail(),
                    'external_id'   => $this->member->getId(),
                    'name'          => $this->member->getFirstName() . ' ' .
                        substr($this->member->getSurname(), 0, 1),
                ]));

                $link = "$link?" . http_build_query([
                    'sso' => $payload,
                    'sig' => hash_hmac('sha256', $payload, $this->settings['sso']['signature']),
                ]);

                break;
            default:
                return $response->withRedirect('/auth/invalid');
        }

        return $this->render($request, $response, 'auth/redirect.html.twig', [
            'href' => $link,
        ]);

    }
}