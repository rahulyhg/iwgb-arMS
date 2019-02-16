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

        $data['token'] = empty($data['token']) ? $request->getQueryParam('token') : $data['token'];

        if (empty($data['token']) ||
            $data['token'] != $key->getToken()) {
            return $response->withRedirect('/auth/invalid');
        }

        // token correct

        if ($key->getTimestamp() < new \DateTime('-1 hour') ||
            $key->isVerified()) {
            return $response->withRedirect('/auth/invalid&e=That verification code has timed out.');
        }

        $data['key'] = empty($data['key']) ? $request->getQueryParam('k') : $data['key'];

        if (empty($data['key']) ||
            $data['key'] != $key->getKey() ||
            $key->getCallback() == 'invalid') {
            return $response->withRedirect('/auth/verify/' . $key->getId() . '?token=' . $key->getToken() . '&e=Invalid key');
        }

        // key correct

        $key->setVerified(true);
        $this->em->flush();

        return $response->withRedirect($key->getCallback() . '?secret=' . $key->getSecret());
    }
}