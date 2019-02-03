<?php

namespace Action\Backend\Settings;

use Action\Backend\GenericLoggedInAction;
use Slim\Http\Request;
use Slim\Http\Response;

class Settings extends GenericLoggedInAction {

    const IGNORE_CONFIGS = [\Config::Dictionary, \Config::App];

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args) {
        $configs = [];
        foreach (\Config::toArray() as $display => $config) {
            if (!in_array($config, self::IGNORE_CONFIGS)) {
                $configs[$display] = [
                    'name'  => $config,
                    'items' => \JSONObject::getMetadata($config),
                ];
            }
        }
        return $this->render($request, $response, 'arms/settings/settings.html.twig', [
            'configs' => $configs,
        ]);
    }
}