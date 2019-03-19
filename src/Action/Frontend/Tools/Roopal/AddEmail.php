<?php

namespace Action\Frontend\Tools\Roopal;

use Action\Frontend\GenericMemberAction;
use Domain\Event;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Http\Request;
use Slim\Http\Response;

class AddEmail extends GenericMemberAction {

    use ParseTrait;

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $data = $request->getParsedBody();

        if (empty($data['zone']) ||
            strpos($data['zone'], ':')) {
            return $response->withRedirect('/member/tools/roopal?e=You must specify your home zone to create a Roopal');
        }

        $id = str_replace('-', '', Uuid::uuid4()->toString());
        $event = new Event('roopal.create', $this->member->getId(), trim($data['zone']) . ':' . $id);
        $this->em->persist($event);
        $this->em->flush();

        $email = 'roopal-' . $id . '@mx.iwgb.org.uk';

        return $response->withRedirect('/member/tools/roopal?m=Roopal created! Send them invoices at: ' . $email);

    }
}