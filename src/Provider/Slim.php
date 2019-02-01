<?php

namespace Provider;

use McAskill\Slim\Polyglot\Polyglot;
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

            $app->add(new Polyglot([
                'languages'         => $c['settings']['languages']['available'],
                'fallbackLanguage'  => $c['settings']['languages']['fallback'],
            ]));

            $app->add($c['csrf']);

            // routes handled by v2

            $app->get('/', \Action\Frontend\Home::class);

            $app->get('/join', \Action\Frontend\Join\ChooseBranch::class);

            //legacy code

            $c['legacy'] = require APP_ROOT . '/legacyConfig.php';
            $container = $c;
            require APP_ROOT . '/legacyConfig.php';
            require_once APP_ROOT . '/legacyApp.php';

            return $app;
        };
    }
}