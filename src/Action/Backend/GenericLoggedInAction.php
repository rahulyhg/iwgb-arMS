<?php

namespace Action\Backend;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericLoggedInAction extends GenericAction {

    /** @var $user \Domain\User */
    protected $user;

    public function __construct(Container $c) {
        parent::__construct($c);
        $this->user = $this->em
            ->getRepository(\Domain\User::class)
            ->find($this->session->get('user'));
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, Response $response, string $template, $vars): ResponseInterface {
        return parent::render($request, $response, $template, array_merge(['user' => $this->user], $vars));
    }
}