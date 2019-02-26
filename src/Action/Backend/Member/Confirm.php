<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Config;
use Doctrine\ORM\ORMException;
use JSONObject;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Confirm extends GenericLoggedInAction {

    const CONFIRMATION_EMAIL_SUBJECT = '';

    const CONFIRMATION_EMAIL_TEXT = '';

    const CONFIRMATION_EMAIL_HTML = [];

    /**
     * {@inheritdoc}
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        /** @var \Domain\Member $member */
        $member = $this->em->getRepository(\Domain\Member::class)->find($args['member']);

        if (empty($member)) {
            return $response->withRedirect('/admin/member/all?e=Member does exist');
        }

        $confirming = !$request->getQueryParam('deconfirm');

        $member->setConfirmed(!$confirming);
        $this->em->flush();

        if ($confirming) {
            $this->send->email->transactional($member->getEmail(),
                self::CONFIRMATION_EMAIL_SUBJECT,
                self::CONFIRMATION_EMAIL_TEXT,
                self::CONFIRMATION_EMAIL_HTML,
                [
                    'application' => $member->getId(),
                    'name' => $member->getFirstName(),
                    //TODO dynamic language (issue 10)
                    'unionCopy' => JSONObject::get(Config::Pages, 'confirmationemail')['elements'][0]['fields']['display']['en'],
                    'branchCopy' => JSONObject::get(Config::Branches, $member->getBranch())['welcome-text']['display']['en'],
                ]
            );
        }

        return $response->withRedirect('/admin/member/' . $member->getId() . '?m=Member confirmation status changed&u=/admin/member/' . $member->getId() . '/confirm%3Fdeconfirm%3D' . ($member->isConfirmed() ? 1 : 0));
    }
}