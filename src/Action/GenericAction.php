<?php

namespace Action;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericAction {

    protected $view;
    protected $em;
    protected $csrf;
    protected $notFoundHandler;

    public function __construct(Container $c) {
        /* @var $c \TypeHinter */
        $this->view = $c->view;
        $this->csrf = $c->csrf;
        $this->em = $c->em;
        $this->notFoundHandler = $c->notFoundHandler;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return mixed
     */
    abstract public function __invoke(Request $request, Response $response, $args);

    /**
     * @param Request $request
     * @param Response $response
     * @param string $template
     * @param mixed[] $vars
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render(Request $request, Response $response, string $template, $vars) {
        return $this->view->render($response, $template,
            array_merge($vars, [
                'csrfValues' => [
                    'name' => $this->csrf->getTokenNameKey(),
                    'value' => $this->csrf->getTokenValueKey(),
                ],
            ])
        );
    }


}