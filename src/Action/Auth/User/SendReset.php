<?php

namespace Action\Auth\User;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class SendReset extends GenericAction {

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();

        if (empty($data['email'])) {
            return $response->withRedirect('/auth/invalid');
        }

        /** @var \Domain\User $user */
        $user = $this->em->getRepository(\Domain\User::class)->find($data['email']);

        if (!empty($user)) {
            $key = new \Domain\VerificationKey('/login/official/reset',
                \KeyType::Email,
                $user->getEmail());
            $this->em->persist($key);
            $this->em->flush();

            $key->send($this->send);

            $event = new \Domain\Event('user.passwordreset', $user->getEmail(), $key->getSecret());
            $this->em->persist($event);
            $this->em->flush();

            $key->setCallback('/auth/login/official/reset/' . $event->getId());

            $this->em->flush();
        }

        return $response->withRedirect('/auth/login/official/reset/sent');
    }
}