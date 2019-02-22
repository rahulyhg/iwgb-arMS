<?php

namespace Action\Auth;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginUser extends GenericAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        $callback = $request->getQueryParam('callback');
        $callback = empty($callback) ? '/' : $callback;

        $data = $request->getParsedBody();

        if (empty($data['user']) ||
            empty($data['pass'])) {
            return self::responseError($response, $callback);
        }

        /** @var \Domain\User $user */
        $user = $this->em->getRepository(\Domain\User::class)->find($data['user']);
        if (empty($user)) {
            return self::responseError($response, $callback);
        }

        // User found
        if ($user->verifyPassword($data['pass'])) {
            $this->session->clear()
                ->set('user', $user->getEmail())
                ->set('name', $user->getName()) //legacy
                ->set('loginStatus', true)
                ->set('realm', 'official');
        } else {
            return self::responseError($response, $callback);
        }

        return $response->withRedirect($callback);
    }

    private static function responseError(Response $response, string $callback): ResponseInterface {
        return $response->withRedirect("/auth/login/official?callback=$callback&e=We couldn't authorise those credentials");
    }
}