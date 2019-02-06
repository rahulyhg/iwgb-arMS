<?php

namespace Action\Frontend\Join\Verify;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Verify extends \Action\Frontend\GenericMemberAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $member = $this->getMember($args['application']);

        // if is post, redirect to get

        // if k and t are set, attempt verification

        // if no more verifications are associated with this application, redirect

        // else, display



    }

    private function verify(string $k, string $t): bool {

    }
}