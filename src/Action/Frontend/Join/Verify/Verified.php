<?php

namespace Action\Frontend\Join\Verify;

use Action\Frontend\GenericMemberAction;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Verified extends GenericMemberAction {

    const VERIFIED_EMAIL_HTML= [
        'content' => [
            'before' => [
                'Application number: %application%',
                'Hey %name%,',
                "We've verified and received your application. Once you've completed the your payment, we'll be in touch confirming your membership to the IWGB!",
            ],
            'after' => [
                "If you've already set up your direct debit on our website, then feel free to ignore this email.",
                "- Your friends at the IWGB",
            ],
            'footer' => [
                'This email was sent because you completed an application on [iwgb.org.uk](https://iwgb.org.uk)',
                "Independent Workers Union of Great Britain  \r\n12-20 Baron St  \r\nLondon  \r\nN1 9LL",
            ]
        ],
        'action' => [
            'href' => 'https://iwgb.org.uk/join/%application%/verified',
            'display' => 'Complete payment',
        ],
    ];

    const VERIFIED_EMAIL_TEXT = "Application number: %application%\r\n\r\nHey %name,\r\nWe've verified and received your application. Once you've completed the your payment, we'll be in touch confirming your membership to the IWGB!\r\n\r\nClick here to complete your payment: https://iwgb.org.uk/join/%application%/verified\r\n\r\nIf you've already set up your direct debit on our website, then feel free to ignore this email.\r\n\r\n- Your friends at the IWGB\r\n\r\nThis email was sent because you completed an application on iwgb.org.uk.\r\nIndependent Workers Union of Great Britain, 12-20 Baron St, London, N1 9LL";

    const VERIFIED_EMAIL_SUBJECT = 'Your IWGB Membership Application';

    /**
     * {@inheritdoc}
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $member = $this->getMember($args['application']);

        // check if verified, if not redirect to /verify

        if (!$member->isConfirmed()) {
            $member->setConfirmed(true);
        }
        $this->em->flush();

        $result = $this->sendTransactionalEmail($member->getEmail(),
            self::VERIFIED_EMAIL_SUBJECT,
            self::VERIFIED_EMAIL_TEXT,
            self::VERIFIED_EMAIL_HTML,
            [
                'name'          => $member->getFirstName(),
                'application'   => $member->getId(),
            ]);

        // render page
    }


}