<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericMemberAction;
use Action\Frontend\GenericPublicAction;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Domain\Member;
use Domain\MemberRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Verified extends GenericPublicAction {

    const ADMIN_EMAIL_ENABLED = false;

    const ADMINISTRATOR_EMAIL_HTML = [
        'content' => [
            'before' => [
                'Hey %name%,',
                'A new member has applied to be a member of the IWGB.',
                "**Ref:** %application%  \r\n**Branch:** %branch%",
                'Go to the new memberships control panel to review their application.',
            ],
            'after' => [
                '— IWGB mailer bot',
            ],
            'footer' => [
                'This email was sent because your [iwgb.org.uk](https://iwgb.org.uk) account is set as a membership administrator.',
            ],
        ],
        'action' => [
            'href'      => 'https://iwgb.org.uk/admin/member/all',
            'display'   => 'View recent applications',
        ],
    ];

    const ADMINISTRATOR_EMAIL_TEXT = "Ref: %application% (%branch%)\r\n\r\nHey %name%,\r\n\r\nA new member has applied to be a member of the IWGB. Go to the new memberships control panel to review their application: https://iwgb.org.uk/admin/member/all\r\n\r\n—the IWGB mailer bot\r\n\r\nThis email was sent because your iwgb.org.uk account is set as a membership administrator.\r\nIndependent Workers Union of Great Britain, 12-20 Baron St, London, N1 9LL";

    const ADMINISTRATOR_EMAIL_SUBJECT = "New membership application";

    /**
     * {@inheritdoc}
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __invoke(Request $request, Response $response, array $args) {
        /** @var \Domain\Member $member */
        $member = $this->em->getRepository(\Domain\Member::class)->find($args['application']);
        if (empty($member)) {
            return $this->notFoundHandler;
        }

        if (!$member->isVerified()) {
            if ($request->getQueryParam('secret') != $member->getRecentSecret()) {
                return $response->withRedirect('/auth/invalid');
            }
            $member->setVerified(true);
            $this->em->flush();

            if (self::ADMIN_EMAIL_ENABLED) {
                foreach ($this->em->getRepository(\Domain\User::class)
                             ->findBy(['membershipAdministrator' => true])
                         as $memberAdmin) {
                    /** @var $memberAdmin \Domain\User */
                    $this->send->email->transactional($memberAdmin->getEmail(),
                        self::ADMINISTRATOR_EMAIL_SUBJECT,
                        self::ADMINISTRATOR_EMAIL_TEXT,
                        self::ADMINISTRATOR_EMAIL_HTML,
                        [
                            'name' => $memberAdmin->getFirstName(),
                            'application' => $member->getId(),
                            'branch' => $member->getBranch(),
                        ]);
                }
            }


            return $response->withRedirect('/join/application/' . $member->getId() . '/verified');
        }

        // render page
        $membership = \JSONObject::findItem(
            \JSONObject::get(\Config::Branches, $member->getBranch())['costs'],
            $member->getMembership());

        return $this->render($request, $response, 'join/verified.html.twig', [
            'membership' => $membership,
            'member'     => $member,
        ]);
    }


}