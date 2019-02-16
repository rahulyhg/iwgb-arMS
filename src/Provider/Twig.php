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

            // Filters

            $env = $view->getEnvironment();

            $env->addFilter(new \Twig_Filter('htmlentities', function (string $s): string {
                return htmlentities($s, ENT_QUOTES, "UTF-8");
            }, ['is_safe' => ['html']]));

            $env->addFilter(new \Twig_Filter('md', function (string $s): string {
                return (new \Parsedown())->text($s);
            }));

            $env->addFilter(new \Twig_Filter('timeago', function (string $s): string {
                return (new \Westsworld\TimeAgo())->inWordsFromStrings($s);
            }));

            $env->addFunction(new \Twig_Function('_i', function (string $s) use ($c): string {
               return 'http://' . $c['settings']['cdn']['baseUrl'] . $s;
            }));


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