<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericPublicAction;
use Domain\Member;
use Psr\Http\Message\ResponseInterface;
use ReCaptcha\ReCaptcha;
use Slim\Http\Request;
use Slim\Http\Response;

class Submit extends GenericPublicAction {

    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\ORMException
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {
        $data = $request->getParsedBody();
        $valid = true;

        // verify application

        $application = self::validateApplication($data);
        if ($application === false) {
            return $response->withRedirect('/join');
        }

        // verify captcha
        $captcha = $this->recaptcha->verify($data['g-recaptcha-response'],
                $request->getAttribute('ip_address'));

        if (!$captcha->isSuccess()) {
            return $response->withRedirect('/join');
        }

        // save

        try {
            $member = Member::constructFromData($application);
        } catch (\Exception $e) {
            return $response->withRedirect('/join');
        }

        $this->em->persist($member);
        $this->em->flush();

        // send text key
        new \Domain\VerificationKey($this->send, $member, \KeyType::SMS);

        // redirect
        return $response->withRedirect("/join/$application/verify");
    }

    /**
     * @param mixed[] $data
     * @return array|bool
     */
    private static function validateApplication($data): array {
        $formSections = \JSONObject::get(\Config::Forms, 'join')['sections'];
        $application = [];

        if (empty($application['branch'])) {
            return false;
        }
        $branchFields = \JSONObject::get(\Config::Branches, $application['branch'])['fields'];

        foreach ($formSections as $section) {
            foreach ($section['fields'] as $line) {
                foreach ($line as $field) {
                    if (!empty($data[$field['name']])) {
                        //TODO verify specific field types

                        $application[$field['name']] = $data[$field['name']];
                    } else {
                        if ($field['required']) {
                            return false;
                        }
                    }
                }
            }
        }

        foreach ($branchFields as $field) {
            if (!empty($data[$field['name']])) {
                $application['branch'][$field['name']] = $data[$field['name']];
            } else {
                if ($field['required']) {
                    return false;
                }
            }
        }

        return $application;
    }
}