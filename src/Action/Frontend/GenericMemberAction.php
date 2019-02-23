<?php

namespace Action\Frontend;

use Action\GenericAction;
use Domain\Member;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Http\Message\ResponseInterface;

abstract class GenericMemberAction extends GenericAction {

    protected $member;

    public function __construct(Container $c) {
        parent::__construct($c);

        /** @var Member $member */
        $member = $this->em
            ->getRepository(Member::class)
            ->find($this->session->get('user'));
        $this->member = $member;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, Response $response, string $template, $vars): ResponseInterface {
        return parent::render($request, $response, $template, array_merge(['user' => $this->member], $vars));
    }
}