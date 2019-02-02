<?php

namespace Action\Frontend;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericPublicAction extends \Action\GenericAction {
    protected $nav;

    public function __construct(Container $c) {
        parent::__construct($c);
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