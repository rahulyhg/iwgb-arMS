<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Config;
use Doctrine\ORM\ORMException;
use Domain\Event;
use Domain\Member;
use Domain\MemberRepository;
use JSONObject;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Confirm extends GenericLoggedInAction {

    const CONFIRMATION_EMAIL_SUBJECT = 'Your IWGB membership is confirmed';

    const CONFIRMATION_EMAIL_TEXT = "Application number: %application%\r\n\r\nHey %name,\r\nWe've just confirmed your application to the IWGB. We'll be in touch soon with your membership card.\r\n\r\n%unionCopy%\r\n\r\n%branchCopy%\r\n\r\nIf you've got any further questions, please don't hesitate to give us a call. Hasta la victoria!\r\n\r\n— Your friends at the IWGB\r\n\r\nThis email was sent because you completed an application on iwgb.org.uk.\r\nIndependent Workers Union of Great Britain, 12-20 Baron St, London, N1 9LL";

    const CONFIRMATION_EMAIL_HTML= [
        'content' => [
            'before' => [
                'Application number: %application%',
                'Hey %name%,',
                "We've just confirmed your application to the IWGB. We'll be in touch soon with your membership card.",
            ],
            'after' => [
                '%unionCopy%',
                '%branchCopy%',
                "If you've got any further questions, please don't hesitate to give us a call.",
                'Hasta la victoria!',
                '— Your friends at the IWGB',
            ],
            'footer' => [
                'This email was sent because you completed an application on [iwgb.org.uk](https://iwgb.org.uk)',
            ],
        ],
    ];

    /**
     * {@inheritdoc}
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        /** @var MemberRepository $memberRepo */
        $memberRepo = $this->em->getRepository(Member::class);
        /** @var Member $member */
        $member = $memberRepo->find($args['member'], null, null, true);

        if (empty($member)) {
            return $response->withRedirect('/admin/member/all/0?e=Member does not exist');
        }

        $confirming = !$request->getQueryParam('deconfirm');

        $member->setConfirmed($confirming);

        $event = new Event('member.confirmed', $member->getId(), $this->user->getEmail());

        $this->em->persist($event);
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
                    'branchCopy' => JSONObject::get(Config::Branches, $member->getBranch())['welcome']['en'],
                ]
            );
        }

        return $response->withRedirect('/admin/member/' . $member->getId() . '?m=Member confirmation status changed&u=/admin/member/' . $member->getId() . '/confirm%3Fdeconfirm%3D' . ($member->isConfirmed() ? 1 : 0));
    }
}