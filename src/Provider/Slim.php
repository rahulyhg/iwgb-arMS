<?php

namespace Provider;

use McAskill\Slim\Polyglot\Polyglot;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ReCaptcha\ReCaptcha;
use RKA\Middleware;
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

            $app->add(new Middleware\IpAddress());

            // routes handled by v2

            $app->get('[/]', \Action\Frontend\Home::class);

            $app->group('/join', function(App $app) {

                $app->get('', \Action\Frontend\Join\Join::class);
                $app->post('', \Action\Frontend\Join\Submit::class);

                $app->group('/application/{application}', function (App $app) {

                    $app->get('/verified', \Action\Frontend\Join\Verified::class);
                });

                $app->get('/{branch}', \Action\Frontend\Join\Form::class);
            });

            $app->group('/admin', function (App $app) {

                $app->group('/settings', function (App $app) {

                    $app->get('', \Action\Backend\Settings\Settings::class);
                    $app->get('/{config}/{item}/data/{type}', \Action\Backend\Settings\GetJson::class);
                    $app->get('/{config}/{item}', \Action\Backend\Settings\EditConfig::class);

                });

                $app->group('/member', function (App $app) {

                    $app->get('[/all]', function(Request $request, Response $response) {
                        return $response->withRedirect('/admin/member/all/0');
                    });

                    $app->get('/all/{page}', \Action\Backend\Member\AllMembers::class);

                    $app->get('/{member}', \Action\Backend\Member\Member::class);

                    $app->get('/{member}/confirm', \Action\Backend\Member\Confirm::class);
                    $app->get('/{member}/delete', \Action\Backend\Member\Delete::class);
                    $app->get('/{member}/remind', \Action\Backend\Member\Remind::class);
                });

                $app->group('/media', function (App $app) {
                    $app->get('/upload', \Action\Backend\Media\UploadForm::class);
                    $app->post('/upload', \Action\Backend\Media\Upload::class);

                    $app->get('[/all]', function(Request $request, Response $response) {
                        return $response->withRedirect('/admin/media/all/0');
                    });

                    $app->get('/all/{page}', \Action\Backend\Media\AllMedia::class);

                    $app->get('/{id}', \Action\Backend\Media\Media::class);
                });

            })->add(new \UserAuthMiddleware($c->session));

            $app->get('/s/{shortlink}', \Action\Frontend\Shortlink::class);

            $app->group('/auth', function (App $app) {

                $app->get('/invalid', \Action\Auth\Invalid::class);
                $app->get('/logout', \Action\Auth\Logout::class);

                $app->group('/verify', function (App $app) {

                    $app->get('/{id}/resend', \Action\Auth\Verify\Resend::class);
                    $app->get('/{id}/data', \Action\Auth\Verify\Submit::class);
                    $app->get('/{id}', \Action\Auth\Verify\Verify::class);
                    $app->post('/{id}', \Action\Auth\Verify\Submit::class);

                });

                $app->group('/sso', function (App $app) {

                    $app->get('/redirect/{id}', \Action\Auth\SSO\Redirect::class);
                    $app->get('/{client}', \Action\Auth\SSO\Authorise::class);

                });

                $app->group('/login', function (App $app) {

                    $app->get('', \Action\Auth\Member\LoginForm::class);
                    $app->post('/member', \Action\Auth\Member\Login::class);
                    $app->get('/member/{event}', \Action\Auth\Member\LoginVerified::class);
                    
                    $app->group('/official', function (App $app) {

                        $app->get('', \Action\Auth\User\LoginForm::class);
                        $app->post('', \Action\Auth\User\Login::class);

                        $app->group('/reset', function (App $app) {

                            $app->get('/request', \Action\Auth\User\SendResetForm::class);
                            $app->post('/request', \Action\Auth\User\SendReset::class);
                            $app->get('/sent', \Action\Auth\User\ResetSent::class);

                            $app->get('/{id}', \Action\Auth\User\ResetPasswordForm::class);
                            $app->post('/{id}', \Action\Auth\User\ResetPassword::class);
                        });
                    });
                });
            });

            //legacy code

            $c['legacy'] = require APP_ROOT . '/legacyConfig.php';
            $container = $c;
            require APP_ROOT . '/legacyConfig.php';
            require_once APP_ROOT . '/legacyApp.php';

            return $app;
        };

        $c['recaptcha'] = new ReCaptcha($c['settings']['recaptcha']['secret']);
    }
}