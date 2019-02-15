<?php

namespace Action\Auth;

use Slim\Http\Request;
use Slim\Http\Response;

class Verify extends GenericAuthAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args) {

        /** @var \Domain\VerificationKey $key */
        $key = $this->em->getRepository(\Domain\VerificationKey::class)->find($args['id']);

        if ($key->getToken() != $request->getQueryParam('token')) {
            return $response->withRedirect('/auth/invalid');
        }

        if ($key->isVerified()) {
            return $response->withRedirect($key->getCallback());
        }

        return $this->render($request, $response, 'auth/verify.html.twig', [
            'key' => $key,
        ]);
    }
}