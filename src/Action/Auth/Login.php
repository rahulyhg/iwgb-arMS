<?php

namespace Action\Auth;

use Action\GenericAction;
use Domain\Event;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Login extends GenericAction {

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $callback = $request->getQueryParam('callback');
        $callback = empty($callback) ? '/' : $callback;

        $data = $request->getParsedBody();
        if (empty($data['phone'])) {
            return $response->withRedirect("/auth/login?callback=$callback&e=Enter the phone number associated with your union membership to log in");
        }

        /** @var \Domain\Member $member */
        $member = $this->em->getRepository(\Domain\Member::class)->findBy(['mobile' => $data['phone']]);


        if (!empty($member)) {
            // send text key
            $member = $member[0];
            $event = new Event('login.member', $member->getId(), $callback);
            $this->em->persist($event);
            $this->em->flush();

            $key = new \Domain\VerificationKey('/auth/login/member/' . $event->getId());
            $this->em->persist($key);
            $this->em->flush();

            $key->send($this->send, \KeyType::SMS, $member->getMobile());

            $member->setRecentSecret($key->getSecret());
            $this->em->flush();

            // redirect
            return $response->withRedirect($key->getLink());
        }

        // we must take every number to verification to prevent members and non-members phone numbers being identified
        if (empty($member)) {
            $key = new \Domain\VerificationKey('invalid');
            $this->em->persist($key);
            $this->em->flush();
            return $response->withRedirect($key->getLink());
        }
    }
}