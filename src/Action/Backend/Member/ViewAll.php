<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ViewAll extends GenericLoggedInAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        return $this->render($request, $response, 'admin/entity-list.html.twig', [
            'entityName'    => 'member',
            'entityPlural'  => 'members',
            'entities'      => $this->em->getRepository(\Domain\Member::class)->findAll(),
            'columns'       => ['id', 'name', 'branch'],
        ]);
    }
}