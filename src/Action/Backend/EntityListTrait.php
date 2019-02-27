<?php

namespace Action\Backend;

use Action\GenericAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig_Environment;
use Twig_Function;

trait EntityListTrait {

    public function addEntityListFunctions(Twig_Environment $twigEnv) {

        $twigEnv->addFunction(new Twig_Function('generateLink', function(string $uri, array $a = []) {
            $parts = explode('?', $uri);
            return $parts[0] . '?' .
                http_build_query(array_merge(self::getAssocQuery($parts[1] ?? ''), $a));
        }));

        $twigEnv->addFunction(new Twig_Function('appendQuery', function (string $uri, string $newUri) {
            return $newUri . '?' . (explode('?', $uri)[1] ?? '');
        }));

        $twigEnv->addFunction(new Twig_Function('generateSortLink', function (string $uri, string $column) {
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

        $twigEnv->addFunction(new Twig_Function('resetSort', function (string $uri) {
            $parts = explode('?', $uri);
            $q = self::getAssocQuery($parts[1] ?? '');
            unset($q['sort']);
            unset($q['order']);
            return self::buildQuery($parts[0], $q);
        }));

        $twigEnv->addFunction(new Twig_Function('resetFilters', function (string $uri) {
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