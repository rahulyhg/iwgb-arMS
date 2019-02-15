<?php

namespace Action\Auth;

use Action\GenericAction;
use Doctrine\ORM\ORMException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Domain\VerificationKey;

class Submit extends GenericAction {

    /**
     * {@inheritdoc}
     * @throws \Exception
     * @throws ORMException
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $data = $request->getParsedBody();

        /** @var VerificationKey $key */
        $key = $this->em->getRepository(VerificationKey::class)->find($args['id']);

        if (empty($data['token']) ||
            $data['token'] != $key->getToken()) {
            return $response->withRedirect('/auth/invalid');
        }

        // token correct

        if ($key->getTimestamp() < new \DateTime('-1 hour')) {
            return $response->withRedirect('/auth/invalid&e=That verification key has timed out.');
        }

        if (empty($data['key']) ||
            $data['key'] != $key->getKey()) {
            return $response->withRedirect('/auth/verify/' . $key->getId() . '?token=' . $key->getToken() . '&e=Invalid key');
        }

        // key correct

        $key->setVerified(true);
        $this->em->flush();

        return $response->withRedirect($key->getCallback() . '?secret=' . $key->getSecret());
    }
}