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

        $c['session'] = function (Container $c) {
            return new \SlimSession\Helper;
        };

        $c['slim'] = function (Container $c): App {
            /** @var $c \TypeHinter */
            $app = new App($c);

            $app->add(new Polyglot([
                'languages'         => $c['settings']['languages']['available'],
                'fallbackLanguage'  => $c['settings']['languages']['fallback'],
            ]));

            $app->add($c['csrf']);

            $app->add(new \Slim\Middleware\Session([
                'name' => 'IwgbSession',
                'autorefresh' => true,
                'lifetime' => '1 hour'
            ]));

            // routes handled by v2

            $app->get('/', \Action\Frontend\Home::class);

            $app->group('/join', function(App $app) {

                $app->get('', \Action\Frontend\Join\Join::class);
                $app->get('/{branch}', \Action\Frontend\Join\Form::class);
            });

            $app->group('/admin', function (App $app) {

                $app->group('/settings', function (App $app) {

                    $app->get('', \Action\Backend\Settings\Settings::class);
                    $app->get('/{config}/{item}/data/{type}', \Action\Backend\Settings\GetJson::class);
                    $app->get('/{config}/{item}', \Action\Backend\Settings\EditConfig::class);

                });

            })->add(new \AuthMiddleware($c->session));


            //legacy code

            $c['legacy'] = require APP_ROOT . '/legacyConfig.php';
            $container = $c;
            require APP_ROOT . '/legacyConfig.php';
            require_once APP_ROOT . '/legacyApp.php';

            return $app;
        };
    }
}