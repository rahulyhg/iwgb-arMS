<?php

namespace Action\Frontend\Join\Verify;

use Doctrine\ORM\ORMException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Verify extends \Action\Frontend\GenericMemberAction {

    /**
     * {@inheritdoc}
     * @throws ORMException
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $member = $this->getMember($args['application']);

        // if is post, redirect to get
        if ($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();
            if (count($data) > 0) {
                for ($i = 0; $i < count($data['k']); $i++) {
                    $member->verify($data['k'][$i], $data['t'][$i]);
                    $this->em->flush();
                }
            }
            return $response->withRedirect('/join/application/' . $member->getId() . '/verify');
        }

        $get = $request->getQueryParams();
        if (!empty($get['k']) &&
            !empty($get['t'])) {
            $member->verify($get['k'], $get['t']);
        }

        if ($request->getQueryParam('headless')) {
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