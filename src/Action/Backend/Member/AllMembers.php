<?php

namespace Action\Backend\Member;

use Action\Backend\EntityListTrait;
use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class AllMembers extends GenericLoggedInAction {

    use EntityListTrait;

    public function __construct(Container $c) {
        parent::__construct($c);
        self::addEntityListFunctions($this->view->getEnvironment());
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        /** @var \Domain\MemberRepository $memberRepo */
        $memberRepo = $this->em->getRepository(\Domain\Member::class);

        $members = $memberRepo->getMembers($request->getQueryParam('branch'),
            $args['page'],
            $request->getQueryParam('sort') ?? 'timestamp',
            $request->getQueryParam('order') ?? 'desc',
            $request->getQueryParam('confirmed') ?? false,
            $request->getQueryParam('verified') ?? false);

        return $this->render($request, $response, 'admin/entity-list.html.twig', [
            'entityName'    => 'member',
            'entityPlural'  => 'applications',
            'entities'      => $members,
            'columns'       => [
                'id' => 'id',
                'name' => 'surname',
                'verified' => 'verified',
                'confirmed' => 'confirmed',
                'branch' => 'branch'
            ],
            'page'          => $args['page'],
            '_a'            => ['w' => 'This contains data on membership applications - data is immutable and so may be outdated'],
            'subnav'        => [
                [
                    'display'   => 'View unverified',
                    'param'     => [
                        'unverified' => 1,
                    ],
                    'icon'      => 'fas fa-filter',
                ],
                [
                    'display'   => 'View confirmed',
                    'param'     => [
                        'confirmed' => 1,
                    ],
                    'icon'      => 'fas fa-filter',
                ],
            ],
        ]);
    }
}