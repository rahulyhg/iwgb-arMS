<?php

namespace Action\Backend\Member;

use Action\Backend\GenericLoggedInAction;
use Domain\MemberRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Member extends GenericLoggedInAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        /** @var MemberRepository $memberRepo */
        $memberRepo = $this->em->getRepository(\Domain\Member::class);
        /** @var \Domain\Member $member */
        $member = $memberRepo->find($args['member'], null, null, true, true);

        if (empty($member)) {
            return $response->withRedirect('/admin/member/all/0?e=Member does not exist');
        }

        return $this->render($request, $response, 'admin/entity/member/view.html.twig', [
            'member' => $member,
            'events' => $this->em->getRepository(\Domain\Event::class)->findBy(['who' => $member->getId()], null, 10),
            '_a'     => ['w' => 'Member information on árMS is currently immutable and so may be outdated'],
        ]);
    }
}