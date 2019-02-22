<?php

namespace Action\Auth;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class MemberLoginVerified extends GenericAction {

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        /** @var \Domain\Event $event */
        $event = $this->em->getRepository(\Domain\Event::class)->find($args['event']);

        if (empty($event) ||
            $event->getType() != 'login.member') {
            return $response->withRedirect('/auth/invalid');
        }

        // event valid

        /** @var \Domain\Member $member */
        $member = $this->em->getRepository(\Domain\Member::class)->find($event->getWho());

        if ($request->getQueryParam('secret') != $member->getRecentSecret()) {
            return $response->withRedirect('/auth/invalid');
        }

        // secret valid

        $this->session->clear()
            ->set('user', $member->getId())
            ->set('loginStatus', true)
            ->set('realm', 'member');

        return $response->withRedirect($event->getData());
    }
}