<?php

namespace Action\Backend\Settings;

use Action\Backend\GenericLoggedInAction;
use Slim\Http\Request;
use Slim\Http\Response;

class EditConfig extends GenericLoggedInAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args) {
        // TODO: if is valid config
        $config = $args['config'];
        $item = $args['item'];
        return $response->withRedirect("/config/edit/?config=$config/$item", 302);
    }
}