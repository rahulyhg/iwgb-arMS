<?php

namespace Action\Auth\SSO;

use Action\GenericAction;
use Domain\Event;
use Slim\Http\Request;
use Slim\Http\Response;

class Authorise extends GenericAction {

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args) {

        $params = $request->getQueryParams();
        $data = '';

        switch (strtolower($args['client'])) {
            case Clients::Discourse:
                if (empty($params['sso']) ||
                    empty($params['sig']) ||
                    !hash_equals(hash_hmac('sha256', $params['sso'], $this->settings['sso']['signature']),
                        $params['sig'])) {
                    return $response->withRedirect('/auth/invalid');
                }

                $data = explode('=', base64_decode($params['sso']))[1];
                break;

            default:
                return $response->withRedirect('/auth/invalid');
                break;
        }

        $event = new Event('sso.callback', $args['client'], $data);
        $this->em->persist($event);
        $this->em->flush();

        return $response->withRedirect('/auth/login?callback=/auth/sso/redirect/' . $event->getId());
    }
}