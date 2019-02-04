<?php

namespace Action\Backend\Settings;

use Action\Backend\GenericLoggedInAction;
use Slim\Http\Request;
use Slim\Http\Response;

class EditConfig extends GenericLoggedInAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args) {

        //TODO validate args

        return $this->render($request, $response, '/admin/settings/edit.html.twig', [
            'config'=> $args['config'],
            'item'  => '',
        ]);
    }
}