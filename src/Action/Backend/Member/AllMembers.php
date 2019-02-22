<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AllMembers extends GenericLoggedInAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        /** @var \Domain\MemberRepository $memberRepo */
        $memberRepo = $this->em->getRepository(\Domain\Member::class);

        return $this->render($request, $response, 'admin/entity-list.html.twig', [
            'entityName'    => 'member',
            'entityPlural'  => 'members',
            'entities'      => $memberRepo->getMembers($request->getQueryParam('branch'), $args['page']),
            'columns'       => ['id', 'name', 'confirmed', 'branch'],
            'page'          => $args['page'],
        ]);
    }
}