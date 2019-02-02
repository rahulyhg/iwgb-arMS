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
    protected $settings;

    public function __construct(Container $c) {
        /* @var $c \TypeHinter */
        $this->view = $c->view;
        $this->csrf = $c->csrf;
        $this->em = $c->em;
        $this->notFoundHandler = $c->notFoundHandler;
        $this->settings = $c->settings;
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
        $requestLanguage = $response->getHeader('Content-Language')[0];
        $fallbackLanguage = $this->settings['languages']['fallback'];
        $dictionary = new \LanguageDictionary($requestLanguage, $fallbackLanguage);

        $twigEnv = $this->view->getEnvironment();


        $twigEnv->addGlobal('_lang', $requestLanguage);
        $twigEnv->addGlobal('_fallback', $fallbackLanguage);

        $twigEnv->addFunction(new \Twig_Function('_', function ($content) use ($dictionary) {
            return $dictionary->get($content);
        }));

        $nameKey = $this->csrf->getTokenNameKey();
        $valueKey = $this->csrf->getTokenValueKey();
        $twigEnv->addGlobal('_csrf', [
            'keys' => [
                'name'  => $nameKey,
                'value' => $valueKey,
            ],
            'values' => [
                'name'  => $request->getAttribute($nameKey),
                'value' => $request->getAttribute($valueKey),
            ]
        ]);

        return $this->view->render($response, $template,
            array_merge($vars, [
                'app' => \JSONObject::get(\Config::App, 'app'),
            ])
        );
    }


}