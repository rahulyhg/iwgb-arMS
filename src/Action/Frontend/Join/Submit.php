<?php

namespace Action\Frontend\Join;

use Action\Frontend\GenericPublicAction;
use Config;
use Domain\Member;
use JSONObject;
use Psr\Http\Message\ResponseInterface;
use Sentry;
use Sentry\State\Scope;
use Slim\Http\Request;
use Slim\Http\Response;

class Submit extends GenericPublicAction {

    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {
        $callback = $request->getQueryParam('callback');
        $callback = empty($callback) ? '/join' : $callback;

        $data = $request->getParsedBody();

        if (!empty($data['email'])) {
            Sentry\configureScope(function (Scope $scope) use ($data): void {
                $scope->setUser(
                    ['email' => $data['email']]);
            });
        }

        // verify application
        $application = self::validateApplication($data);
        if ($application === false) {
            return $response->withRedirect($callback);
        }

        if ($application['gender'] == 'other' &&
            !empty($application['gender-other'])) {
            $application['gender'] = $application['gender-other'];
        }

        if ($application['language'] == 'other' &&
            !empty($application['language-other'])) {
            $application['language'] = $application['language-other'];
        } else if ($application['language'] == 'did-not-supply') {
            $application['language'] = $response->getHeader('Content-Language')[0];
        }

        if ($application['ethnicity'] == 'other' &&
            !empty($application['ethnicity-other'])) {
            $application['ethnicity'] = $application['ethnicity-other'];
        }

        // verify captcha
        $captcha = $this->recaptcha->verify($data['g-recaptcha-response'],
                $request->getAttribute('ip_address'));

        if (!$captcha->isSuccess()) {
            return $response->withRedirect($callback);
        }

        // save

        try {
            $member = Member::constructFromData($application);
        } catch (\Exception $e) {
            return $response->withRedirect($callback);
        }

        $this->em->persist($member);
        $this->em->flush();

        // send text key
        $key = new \Domain\VerificationKey('/join/application/' . $member->getId() . '/verified',
            \KeyType::SMS,
            $member->getMobile());
        $this->em->persist($key);
        $this->em->flush();
        $key->send($this->send);

        $member->setRecentSecret($key->getSecret());
        $this->em->flush();

        // redirect
        return $response->withRedirect($key->getLink());
    }

    /**
     * @param mixed[] $data
     * @return array|bool
     */
    private static function validateApplication($data) {
        $formSections = JSONObject::get(Config::Forms, 'join')['sections'];

        // mandatory
        $formSections[]['fields'] = [
            [[
                'name'      => 'branch',
                'required'  => true,
            ]],
            [[
                'name'      => 'membership',
                'required'  => true,
            ]],
        ];

        $application = [];

        if (empty($data['branch'])) {
            return false;
        }
        $branchFields = JSONObject::get(Config::Branches, $data['branch'])['fields'];

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
                $application['branchData'][$field['name']] = $data[$field['name']];
            } else {
                if ($field['required']) {
                    return false;
                }
            }
        }
        $application['branch'] = $data['branch'];

        return $application;
    }
}