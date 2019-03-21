<?php

namespace Action\Backend;

use Twig;
use Twig\TwigFunction;

trait EntityListTrait {

    public function addEntityListFunctions(Twig\Environment $twigEnv) {

        $twigEnv->addFunction(new TwigFunction('generateLink', function(string $uri, array $a = []) {
            $parts = explode('?', $uri);
            return $parts[0] . '?' .
                http_build_query(array_merge(self::getAssocQuery($parts[1] ?? ''), $a));
        }));

        $twigEnv->addFunction(new TwigFunction('appendQuery', function (string $uri, string $newUri) {
            return $newUri . '?' . (explode('?', $uri)[1] ?? '');
        }));

        $twigEnv->addFunction(new TwigFunction('generateSortLink', function (string $uri, string $column) {
            $parts = explode('?', $uri);
            $q = self::getAssocQuery($parts[1] ?? '');
            if (!empty($q['sort']) &&
                $q['sort'] == $column) {
                $q['order'] = $q['order'] == 'asc' ? 'desc' : 'asc';
            } else {
                $q['sort'] = $column;
                $q['order'] = 'asc';
            }
            return self::buildQuery($parts[0], $q);
        }));

        $twigEnv->addFunction(new TwigFunction('resetSort', function (string $uri) {
            $parts = explode('?', $uri);
            $q = self::getAssocQuery($parts[1] ?? '');
            unset($q['sort']);
            unset($q['order']);
            return self::buildQuery($parts[0], $q);
        }));

        $twigEnv->addFunction(new TwigFunction('resetFilters', function (string $uri) {
            $parts = explode('?', $uri);
            $q = self::getAssocQuery($parts[1] ?? '');
            $q = array_intersect_key($q, ['sort' => null, 'order' => null]);
            return self::buildQuery($parts[0], $q);
        }));
    }

    private static function getAssocQuery(string $query) {
        $vars = [];
        parse_str($query, $vars);
        return $vars;
    }

    private static function buildQuery(string $uri, array $query): string {
        return $uri . '?' . http_build_query($query);
    }
}