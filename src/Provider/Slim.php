<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class Slim implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Container $c) {

        $c['csrf'] = function (Container $c): \Slim\Csrf\Guard {
            return new \Slim\Csrf\Guard;
        };

        $c['notFoundHandler'] = function (Container $c) {
            return function (Request $request, Response $response) use ($c) {
                /** @noinspection PhpUndefinedFieldInspection */
                return $c->view->render($response, '404.html.twig')->withStatus(404);
            };
        };

        $c['slim'] = function (Container $c): App {
            /** @var $c \TypeHinter */
            $app = new App($c);

            $app->add(new \McAskill\Slim\Polyglot\Polyglot([
                'languages'         => $c['settings']['languages']['available'],
                'fallbackLanguage'  => $c['settings']['languages']['fallback'],
            ]));

            $app->add($c['csrf']);

            // routes handled by v2

            $app->get('/', \Action\Frontend\Home::class);

            //legacy code

            $c['legacy'] = require APP_ROOT . '/legacyConfig.php';
            $container = $c;
            include APP_ROOT . '/legacyConfig.php';
            include APP_ROOT . '/legacyApp.php';

            return $app;
        };
    }
}