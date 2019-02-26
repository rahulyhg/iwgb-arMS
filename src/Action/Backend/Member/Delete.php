<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Doctrine\ORM\ORMException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Delete extends GenericLoggedInAction {

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

        if (!$request->getQueryParam('confirm')) {
            return $response->withRedirect('/admin/member/' . $member->getId() . '?w=You cannot undo this action. Do you want to proceed?&c=/admin/member' . $member->getId() . '/delete?confirm=1');
        }
        $this->em->remove($member);
        $this->em->flush();

        return $response->withRedirect('/admin/member/' . $member->getId() . '?m=Member deleted');
    }
}