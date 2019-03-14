<?php

namespace Action\Backend\Member;

use Action\Frontend\GenericPublicAction;
use Config;
use Domain\Member;
use Domain\MemberRepository;
use JSONObject;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Reverify extends GenericPublicAction {

    const REVERIFY_EMAIL_HTML= [
        'content' => [
            'before' => [
                'Application number: %application%',
                'Hey %name%,',
                "You can now verify your application by clicking on the link below and entering the code that we've sent to the contact details you provided.",
            ],
            'after' => [
                '%copy%',
                "If you've got any further questions, please don't hesitate to give us a call.",
                '— Your friends at the IWGB',
            ],
            'footer' => [
                'This email was sent because you completed an application on [iwgb.org.uk](https://iwgb.org.uk)',
            ],
        ],
        'action' => [
            'href' => '%link%',
            'display' => 'Verify your application',
        ],
    ];

    const REVERIFY_EMAIL_TEXT = "Application number: %application%\r\n\r\nHey %name,\r\nYou can now verify your application by clicking on the link below and entering the code that we've sent to the contact details you provided.\r\n\r\nClick here to verify your application: %link%\r\n\r\n%copy%\r\n\r\nIf you've got any further questions, please don't hesitate to give us a call.\r\n\r\n— Your friends at the IWGB\r\n\r\nThis email was sent because you completed an application on iwgb.org.uk.\r\nIndependent Workers Union of Great Britain, 12-20 Baron St, London, N1 9LL";

    const REVERIFY_EMAIL_SUBJECT = 'Verify your IWGB Membership Application';

    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        /** @var MemberRepository $memberRepo */
        $memberRepo = $this->em->getRepository(Member::class);
        /** @var Member $member */
        $member = $memberRepo->find($args['member'], null, null, true, true);

        if (empty($member)) {
            return $response->withRedirect('/admin/member/all/0?e=Member does exist');
        }

        // send text key
        $key = new \Domain\VerificationKey('/join/application/' . $member->getId() . '/verified',
            \KeyType::SMS,
            $member->getMobile());
        $this->em->persist($key);
        $this->em->flush();
        $key->send($this->send);

        $member->setRecentSecret($key->getSecret());
        $this->em->flush();

        $this->send->email->transactional($member->getEmail(),
            self::REVERIFY_EMAIL_SUBJECT,
            self::REVERIFY_EMAIL_TEXT,
            self::REVERIFY_EMAIL_HTML,
            [
                'application'   => $member->getId(),
                'name'          => $member->getFirstName(),
                'link'          => 'https://iwgb.org.uk' . $key->getLink(),
                //TODO dynamic language (issue 10)
                'copy'          => JSONObject::get(Config::Pages, 'reminderemail')['elements'][0]['fields']['display']['en'],
            ]);

        return $response->withRedirect('/admin/member/' . $member->getId() . '?m=Reverification email sent');

    }

}