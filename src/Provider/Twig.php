<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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

            $env->addFilter(new TwigFilter('htmlentities', function (string $s): string {
                return htmlentities($s, ENT_QUOTES, "UTF-8");
            }, ['is_safe' => ['html']]));

            $env->addFilter(new TwigFilter('md', function (string $s): string {
                return (new \Parsedown())->text($s);
            }));

            $env->addFilter(new TwigFilter('timeago', function (string $s): string {
                return (new \Westsworld\TimeAgo())->inWordsFromStrings($s);
            }));

            $env->addFunction(new TwigFunction('_i', function (string $s) use ($c): string {
               return 'https://' . $c['settings']['cdn']['baseUrl'] . $s;
            }));


            if ($debug) {
                $view->addExtension(new TwigExtension(
                    $c['router'],
                    $c['request']->getUri()
                ));
                $view->addExtension(new DebugExtension());
            }
            return $view;
        };
    }
}