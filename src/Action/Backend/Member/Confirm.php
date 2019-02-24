<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Doctrine\ORM\ORMException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Confirm extends GenericLoggedInAction {

    /**
     * {@inheritdoc}
     * @throws ORMException
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        /** @var \Domain\Member $member */
        $member = $this->em->getRepository(\Domain\Member::class)->find($args['member']);

        if (empty($member)) {
            return $response->withRedirect('/admin/member/all?e=Member does exist');
        }

        $member->setConfirmed(!$request->getQueryParam('deconfirm'));
        $this->em->flush();

        return $response->withRedirect('/admin/member/' . $member->getId() . '?m=Member confirmation status changed&u=/admin/member/' . $member->getId() . '/confirm%3Fdeconfirm%3D' . ($member->isConfirmed() ? 1 : 0));
    }
}