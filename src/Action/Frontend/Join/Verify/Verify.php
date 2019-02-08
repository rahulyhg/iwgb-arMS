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
                    $member->verify($verification['k'], $verification['t']);
                }
            }
            return $response->withRedirect('/join/application/' . $member->getId() . '/verify');
        }

        $get = $request->getQueryParams();
        if (!empty($get['k']) &&
            !empty($get['t'])) {
            $member->verify($get['k'], $get['t']);
        }

        if ($get['headless']) {
            return $response->withStatus(200);
        }

        if ($member->isVerified()) {
            return $response->withRedirect('/join/application/' . $member->getId() . '/verified');
        }

        // else, display
        return $this->render($request, $response, '/join/verify.html.twig', [
            'member' => $member,
        ]);
    }

}