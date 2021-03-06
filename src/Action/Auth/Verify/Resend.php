<?php

namespace Action\Auth\Verify;

use Action\GenericAction;
use Domain\VerificationKey;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Resend extends GenericAction {

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        /** @var VerificationKey $key */
        $key = $this->em->getRepository(VerificationKey::class)->find($args['id']);
        $token = $request->getQueryParam('token');

        if (empty($key) ||
            empty($token) ||
            $token != $key->getToken()) {
            return $response->withRedirect('/auth/invalid');
        }

        if ($key->getCallback() == 'invalid') {
            return $response->withRedirect($key->getLink());
        }

        // token correct

        if ($key->getTimestamp() < new \DateTime('-1 hour')) {
            return $response->withRedirect('/auth/invalid&e=That verification code has timed out.');
        }

        // token valid

        $key->send($this->send);
        return $response->withRedirect($key->getLink());


    }
}