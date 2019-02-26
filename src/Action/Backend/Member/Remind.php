<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Doctrine\ORM\ORMException;
use Domain\MemberRepository;
use JSONObject;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Remind extends GenericLoggedInAction {

    const VERIFIED_EMAIL_HTML= [
        'content' => [
            'before' => [
                'Application number: %application%',
                'Hey %name%,',
                "We've verified and received your application. Once you've completed the your payment, we'll be in touch confirming your membership to the IWGB!",
                'If you had difficulties completing your payment on your phone, try using a laptop or desktop. Sorry about that.',
            ],
            'after' => [
                '**Why join the IWGB?**',
                '%copy%',
                "If you've got any further questions, please don't hesitate to give us a call.",
                '— Your friends at the IWGB',
            ],
            'footer' => [
                'This email was sent because you completed an application on [iwgb.org.uk](https://iwgb.org.uk)',
            ],
        ],
        'action' => [
            'href' => 'https://iwgb.org.uk/join/application/%application%/verified',
            'display' => 'Complete payment',
        ],
    ];

    const VERIFIED_EMAIL_TEXT = "Application number: %application%\r\n\r\nHey %name,\r\nWe've verified and received your application. Once you've completed the your payment, we'll be in touch confirming your membership to the IWGB! If you had difficulties completing your payment on your phone, try using a laptop or desktop. Sorry about that.\r\n\r\nClick here to complete your payment: https://iwgb.org.uk/join/%application%/verified\r\n\r\nWhy join the IWGB?\r\n\r\n%copy%\r\n\r\nIf you've got any further questions, please don't hesitate to give us a call.\r\n\r\n— Your friends at the IWGB\r\n\r\nThis email was sent because you completed an application on iwgb.org.uk.\r\nIndependent Workers Union of Great Britain, 12-20 Baron St, London, N1 9LL";

    const VERIFIED_EMAIL_SUBJECT = 'Your IWGB Membership Application';

    /**
     * {@inheritdoc}
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        /** @var MemberRepository $memberRepo */
        $memberRepo = $this->em->getRepository(\Domain\Member::class);
        /** @var \Domain\Member $member */
        $member = $memberRepo->find($args['member'], null, null, true);

        if (empty($member)) {
            return $response->withRedirect('/admin/member/all/0?e=Member does exist');
        }

        $this->send->email->transactional($member->getEmail(),
            self::VERIFIED_EMAIL_SUBJECT,
            self::VERIFIED_EMAIL_TEXT,
            self::VERIFIED_EMAIL_HTML,
            [
                'application'   => $member->getId(),
                'name'          => $member->getFirstName(),
                //TODO dynamic language (issue 10)
                'copy'          => JSONObject::get(\Config::Pages, 'reminderemail')['elements'][0]['fields']['display']['en'],
            ]);

        return $response->withRedirect('/admin/member/' . $member->getId() . '?m=Reminder email sent');

    }
}