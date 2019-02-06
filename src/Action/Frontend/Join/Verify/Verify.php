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
        if ($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();
            if (count($data) > 0) {
                foreach ($data as $verification) {
                    $this->http->submitForm('/join/verify', [
                        'k'         => $verification['k'],
                        't'         => $verification['t'],
                        'headless'  => true,
                    ], 'GET');
                }
            }
            return $response->withRedirect('/join/verify');
        }

        $get = $request->getQueryParams();
        if (!empty($get['k']) &&
            !empty($get['t'])) {
            $this->verify($get['k'], $get['t']);
        }

        if ($get['headless']) {
            return $response;
        }

        // check authy if verified
        if (false /* verified */) {
            return $response->withRedirect('/join/verified')
        }

        // else, display
    }

    private function verify(string $k, string $t): bool {
        // authy verify
    }
}