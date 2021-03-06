<?php

namespace Provider;

use Exception;
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

        $c['errorHandler'] = function (Container $c) {
            $details = $c['settings']['displayErrorDetails'];
            if ($details) {
                return new \Slim\Handlers\Error($details);
            } else {
                return function (Request $request, Response $response, Exception $ex) use ($c) {
                    \Sentry\captureException($ex);
                    return $c['view']->render($response, '500.html.twig', [
                        'event' => \Sentry\State\Hub::getCurrent()->getLastEventId(),
                        'dsn'   => $c['settings']['sentry']['dsn'],
                    ])->withStatus(500);
                };
            }
        };

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

            $app->add(function (Request $request, Response $response, callable $next) {
                $uri = $request->getUri();
                $path = $uri->getPath();
                if ($path != '/' && substr($path, -1) == '/') {
                    $uri = $uri->withPath(substr($path, 0, -1));

                    if($request->getMethod() == 'GET') {
                        return $response->withRedirect((string)$uri, 301);
                    } else {
                        return $next($request->withUri($uri), $response);
                    }
                }
                
                return $next($request, $response);
            });

            $app->add($c['csrf']);

            $app->add(new Polyglot([
                'languages'         => $c['settings']['languages']['available'],
                'fallbackLanguage'  => $c['settings']['languages']['fallback'],
            ]));



            $app->add(new \Slim\Middleware\Session([
                'name' => 'IwgbSession',
                'autorefresh' => true,
                'lifetime' => '1 hour'
            ]));

            $app->add(new Middleware\IpAddress());

            // routes handled by v2

            $app->get('[/]', \Action\Frontend\Home::class);

            // legacy bodge
            $app->get('/uploadheader/{name}/{type}/{ext}', \Action\Backend\Post\Upload::class);

            $app->group('/x', function (App $app) {

                $app->get('/{folder:(?:css|js)}/{file}', \Action\Backend\GetCode::class);
                $app->get('/{folder:(?:css|js)}/{subfolder}/{file}', \Action\Backend\GetCode::class);
            });

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
                    $app->get('/{member}/reverify', \Action\Backend\Member\Reverify::class);
                });

                $app->group('/media', function (App $app) {

                    $app->get('/new', \Action\Backend\Media\UploadForm::class);
                    $app->post('/new', \Action\Backend\Media\Upload::class);
                    $app->post('/new-folder', \Action\Backend\Media\NewFolder::class);

                    $app->group('/{path}', function (App $app) {

                        $app->get('/new-folder', \Action\Backend\Media\NewFolderForm::class);
                        $app->get('/view', \Action\Backend\Media\View::class);
                        $app->get('/delete', \Action\Backend\Media\Delete::class);
                    });
                });

            })->add(new \AuthMiddleware($c->session, 'official'));

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

            $app->group('/member', function (App $app) {

                $app->group('/tools', function (App $app) {

                    $app->get('/roopal', \Action\Frontend\Tools\Roopal\Form::class);
                    $app->post('/roopal', \Action\Frontend\Tools\Roopal\Upload::class);
                });

            })->add(new \AuthMiddleware($c->session, 'member'));

            //legacy code

            $c['legacy'] = require APP_ROOT . '/legacyConfig.php';
            /** @noinspection PhpUnusedLocalVariableInspection */
            $container = $c;
            require APP_ROOT . '/legacyConfig.php';
            require_once APP_ROOT . '/legacyApp.php';

            return $app;
        };

        $c['recaptcha'] = new ReCaptcha($c['settings']['recaptcha']['secret']);
    }
}