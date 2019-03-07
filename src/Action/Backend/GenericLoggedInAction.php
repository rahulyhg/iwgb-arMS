<?php

namespace Action\Backend;

use Action\GenericAction;
use Domain\User;
use Psr\Http\Message\ResponseInterface;
use Sentry\State\Scope;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericLoggedInAction extends GenericAction {

    /** @var $user \Domain\User */
    protected $user;

    public function __construct(Container $c) {
        parent::__construct($c);

        /** @var User $user */
        $user = $this->em
            ->getRepository(User::class)
            ->find($this->session->get('user'));
        $this->user = $user;

        \Sentry\configureScope(function (Scope $scope) use ($user): void {
            $scope->setUser([
                'email' => $user->getEmail(),
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, Response $response, string $template, $vars): ResponseInterface {
        return parent::render($request, $response, $template, array_merge(['user' => $this->user], $vars));
    }
}