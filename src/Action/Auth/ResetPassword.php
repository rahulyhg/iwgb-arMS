<?php

namespace Action\Auth;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ResetPassword extends GenericAction {

    const RESET_CONFIRMATION_SUBJECT = 'Your password has been reset';

    const RESET_CONFIRMATION_HTML = [
        'content' => [
            'before' => [
                'Hey %name%,',
                "We're just writing to confirm the reset of your IWGB account password.",
                "If this wasn't you, then the IWGB Data Protection Policy requires you as a member, officer or employee to contact our Data Protection Officer immediately.",
                "If you haven't already, you can log in using the link below; by clicking Log In at the bottom of most pages; or by visiting any page where log-in is required.",
            ],
            'after' => [
                "Keep your new password safe and secure, and make sure you don't use it for any other accounts.",
                "If you've got any further issues, just contact the tech team.",
                '— Your friends at the IWGB',
            ],
            'footer' => [
                'This email was sent to confirm the reset of your password on [iwgb.org.uk](https://iwgb.org.uk).',
            ],
        ],
        'action' => [
            'href' => 'https://iwgb.org.uk/auth/login',
            'display' => 'Log in',
        ],
        'alert' => [
            'type'      => 'good',
            'display'   => 'Password reset successfully'
        ],
    ];

    const RESET_CONFIRMATION_TEXT = "Hey %name%,\n\nWe're just writing to confirm the reset of your IWGB account password.\n\nIf this wasn't you, then the IWGB Data Protection Policy requires you as a member, officer or employee to contact our Data Protection Officer immediately.\n\nIf you've got any further issues, just contact the tech team.\n\n— Your friends at the IWGB";

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $data = $request->getParsedBody();

        /** @var \Domain\Event $event */
        $event = $this->em->getRepository(\Domain\Event::class)->find($args['id']);

        if (empty($event) ||
            empty($data['pass']) ||
            empty($data['secret']) ||
            $data['secret'] != $event->getData() ||
            $event->getTimestamp() < new \DateTime('-15 minutes')) {
            return $response->withRedirect('/auth/invalid');
        }

        /** @var \Domain\User $user */
        $user = $this->em->getRepository(\Domain\User::class)->find($event->getWho());
        $user->setPass($data['pass']);
        $this->em->flush();

        $this->send->email->transactional($user->getEmail(),
            self::RESET_CONFIRMATION_SUBJECT,
            self::RESET_CONFIRMATION_TEXT,
            self::RESET_CONFIRMATION_HTML,
            [
                'name' => $user->getFirstName(),
            ]);

        return $response->withRedirect('/auth/login?m=Password reset successfully');
    }
}