<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Member extends GenericLoggedInAction {

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {

        return $this->render($request, $response, 'admin/entity/member/view.html.twig', [
            'member' => $this->em->getRepository(\Domain\Member::class)->find($args['member']),
        ]);
    }
}