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
            $templates = $c['settings']['twig']['templates_dir'];
            $cache = $c['settings']['twig']['cache_dir'];
            $debug = $c['settings']['twig']['debug'];

            $view = new \Slim\Views\Twig($templates, compact('cache', 'debug'));

            $view->getEnvironment()->addGlobal('_get', $_GET);
            $view->getEnvironment()->addGlobal('csrfKeys', [
                'name'  => $c['csrf']->getTokenNameKey(),
                'value' => $c['csrf']->getTokenValueKey(),
            ]);

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