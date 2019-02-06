<?php

namespace Action;

use Mailgun\Model\Message\SendResponse;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericAction {

    /** @var $view \Slim\Views\Twig */
    protected $view;

    /** @var $notFound callable */
    protected $notFoundHandler;

    /** @var $em \Doctrine\ORM\EntityManager */
    protected $em;

    /** @var $slim \Slim\App */
    protected $slim;

    /** @var $twilio \Twilio\Rest\Client */
    protected $twilio;

    /** @var  $csrf \Slim\Csrf\Guard */
    protected $csrf;

    /** @var $session \SlimSession\Helper */
    protected $session;

    /** @var $http \Buzz\Browser */
    protected $http;

    /** @var $settings array */
    protected $settings;

    /** @var $mailgun \Mailgun\Mailgun */
    protected $mailgun;

    public function __construct(Container $c) {
        /* @var $c \TypeHinter */
        $this->view = $c->view;
        $this->csrf = $c->csrf;
        $this->em = $c->em;
        $this->notFoundHandler = $c->notFoundHandler;
        $this->settings = $c->settings;
        $this->session = $c->session;
        $this->http = $c->http;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return mixed
     */
    abstract public function __invoke(Request $request, Response $response, $args): ResponseInterface;

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

    protected function sendEmail(array $params): SendResponse {
        return $this->mailgun->messages()->send($this->settings['mailgun']['domain'],
            array_merge([
                'from'      => $this->settings['mailgun']['from'],
                'h:Reply-To'=> $this->settings['mailgun']['replyTo'],
                ], $params)
            );
    }

    protected function sendTransactionalEmail(string $to, string $subject, string $text, array $htmlVars, array $params): SendResponse {
        $email = $this->view->getEnvironment()->load('/email/transaction.html.twig');
        $html = $email->render(array_merge([$subject], $htmlVars));
        return $this->sendEmail([
            'to'        => $params['to'],
            'subject'   => $params['subject'],
            'text'      => self::processEmailText($text, $params),
            'html'      => self::processEmailText($html, $params),
        ]);
    }

    private static function processEmailText($body, $params): string {
        foreach ($params as $key => $value) {
            $body = str_replace("%$key%", $value, $body);
        }
        return $body;
    }


}