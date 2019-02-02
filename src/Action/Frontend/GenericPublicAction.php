<?php

namespace Action\Frontend;

use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericPublicAction extends \Action\GenericAction {
    protected $nav;

    public function __construct($container) {
        parent::__construct($container);
        $this->nav = \JSONObject::get(\Config::Menus, 'public-top');
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, Response $response, string $template, $vars) {
        return parent::render($request,$response, $template,
            array_merge($vars, ['nav' => $this->nav]));
    }
}