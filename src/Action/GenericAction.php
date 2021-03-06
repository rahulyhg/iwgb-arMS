<?php

namespace Action;

use Psr\Http\Message\ResponseInterface;
use Sentry;
use Sentry\State\Scope;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig\TwigFunction;

abstract class GenericAction {

    protected $view;

    protected $notFoundHandler;

    protected $em;

    protected $slim;

    protected $send;

    protected $csrf;

    protected $session;

    protected $http;

    protected $settings;

    protected $recaptcha;

    protected $cdn;

    public function __construct(Container $c) {
        /* @var $c \TypeHinter */
        $this->view = $c->view;
        $this->csrf = $c->csrf;
        $this->em = $c->em;
        $this->notFoundHandler = $c->notFoundHandler;
        $this->settings = $c->settings;
        $this->session = $c->session;
        $this->http = $c->http;
        $this->send = $c->send;
        $this->recaptcha = $c->recaptcha;
        $this->cdn = $c->cdn;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return mixed
     */
    abstract public function __invoke(Request $request, Response $response, array $args);

    /**
     * @param Request $request
     * @param Response $response
     * @param string $template
     * @param mixed[] $vars
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render(Request $request, Response $response, string $template, $vars): ResponseInterface {
        $requestLanguage = $response->getHeader('Content-Language')[0];
        $fallbackLanguage = $this->settings['languages']['fallback'];
        $dictionary = new \LanguageDictionary($requestLanguage, $fallbackLanguage);

        $twigEnv = $this->view->getEnvironment();

        $twigEnv->addGlobal('_lang', $requestLanguage);
        $twigEnv->addGlobal('_fallback', $fallbackLanguage);
        $twigEnv->addGlobal('_get', $request->getQueryParams());
        $twigEnv->addGlobal('_uri', $request->getUri()->getPath() . '?' . $request->getUri()->getQuery());
        $twigEnv->addGlobal('_mode', 'v2');

        $twigEnv->addFunction(new TwigFunction('_', function ($content) use ($dictionary) {
            return $dictionary->get($content);
        }));

        $twigEnv->addFunction(new TwigFunction('_a', function ($s) use ($dictionary) {
            return $dictionary->processLink($s);
        }));

        Sentry\configureScope(function (Scope $scope) use ($request): void {
            $scope->setUser(
                ['ip' => $request->getAttribute('ip_address')]);
        });

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