<?php

namespace Action\Frontend\Join\Verify;

use Action\Frontend\GenericMemberAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Verified extends GenericMemberAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $member = $this->getMember($args['application']);

        // check if verified, if not redirect to /verify

        // if verified, get confirmation status from em. if not confirmed, make confirmed and send confirmation email

        // render page
    }
}