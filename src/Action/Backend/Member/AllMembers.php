<?php

namespace Action\Backend\Member;

use Action\Backend\GenericEntityListAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AllMembers extends GenericEntityListAction {

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
            'columns'       => ['id', 'name', 'verified', 'confirmed', 'branch'],
            'page'          => $args['page'],
            '_a'            => ['w' => 'Member information on árMS is currently immutable and so may be outdated'],
            'subnav'        => [
                [
                    'display' => 'View unverified',
                    'href' => '#',
                    'icon' => 'fas fa-filter',
                ],
                [
                    'display' => 'View confirmed',
                    'href' => '#',
                    'icon' => 'fas fa-filter',
                ],
            ],
        ]);
    }
}