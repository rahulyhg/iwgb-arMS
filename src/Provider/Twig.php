<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Twig implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Container $c) {

        $c['view'] = function (Container $c): \Slim\Views\Twig {
            /* @var $c \Slim\Container */
            $templates = $c['settings']['twig']['templates_dir'];
            $cache = $c['settings']['twig']['cache_dir'];
            $debug = $c['settings']['twig']['debug'];

            $view = new \Slim\Views\Twig($templates, compact('cache', 'debug'));

            $twigEnv = $view->getEnvironment();

            $twigEnv->addGlobal('_get', $_GET);
            $twigEnv->addGlobal('_csrf', [
                'name'  => $c['csrf']->getTokenNameKey(),
                'value' => $c['csrf']->getTokenValueKey(),
            ]);
            $twigEnv->addGlobal('_fallback', $c['settings']['languages']['fallback']);

            if ($debug) {
                $view->addExtension(new \Slim\Views\TwigExtension(
                    $c['router'],
                    $c['request']->getUri()
                ));
                $view->addExtension(new \Twig_Extension_Debug());
            }
            return $view;
        };
    }
}