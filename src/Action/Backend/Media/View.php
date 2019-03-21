<?php

namespace Action\Backend\Media;

use Action\Backend\EntityListTrait;
use Action\Backend\GenericLoggedInAction;
use Aws\Api\DateTimeResult;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig\TwigFunction;

class View extends GenericLoggedInAction {

    use EntityListTrait;
    use SpacesActionTrait;

    public function __construct(Container $c) {
        parent::__construct($c);
        $twigEnv = $this->view->getEnvironment();
        self::addEntityListFunctions($twigEnv);

        /** @var $c \TypeHinter */
        $twigEnv->addFunction(new TwigFunction('isImage', function($s) {
            return self::isImage($s);
        }));
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        if ($args['path'] == 'root') {
            return $response->withRedirect('/admin/media/' . $this->getEncodedRoot() . '/view');
        }

        $prefix = base64_decode($args['path']);


        $params = [
            'Bucket' => $this->settings['spaces']['bucket'],
            'Prefix' => $prefix,
        ];

        $objects = $this->cdn->listObjects($params)->toArray()['Contents'];

        $parsedObjects = [];
        foreach ($objects as $object) {
            if ($object['Key'] != $prefix &&
                preg_match('/^' . str_replace('/', '\/', $prefix) . '[a-zA-Z0-9\-.]+\/?$/', $object['Key'])) {

                /** @var DateTimeResult $modified */
                $modified = $object['LastModified'];

                if (substr($object['Key'], -1) == '/') {
                    $type = 'folder';
                } else {
                    $type = 'object';
                }

                $parsedObjects[] = [
                    'id' => base64_encode($object['Key']),
                    'key' => $object['Key'],
                    'name' => str_replace($prefix, '', $object['Key']),
                    'modified' => $modified->__toString(),
                    'type' => $type,
                ];
            }
        }

        usort($parsedObjects, function ($a, $b) {
            if ($a['type'] == $b['type']) {
                return strcmp($a['name'], $b['name']);
            } else {
                return $a['type'] == 'folder' ? -1 : 1;
            }
        });

        if ($prefix != $this->getRoot()) {

            $parent = substr($prefix, 0,
                strrpos($prefix, '/', -2) + 1);

            $parsedObjects = array_merge([[
                'id' => base64_encode($parent),
                'key' => $parent,
                'name' => '..',
                'modified' => (new DateTime())->format(DateTime::ATOM),
                'type' => 'folder',
            ]], $parsedObjects);
}

        return $this->render($request, $response, 'admin/entity-list.html.twig', [
            'entityName'    => 'media',
            'entityPlural'  => str_replace(substr($this->getRoot(), 0, -1), '', $prefix),
            'entities'      => $parsedObjects,
            'columns'       => [
                'name' => 'key',
                'modified' => 'modified',
            ],
            'subnav'        => [
                [
                    'display'   => 'New folder',
                    'href'      => '/admin/media/' . $args['path'] . '/new-folder',
                    'icon'      => 'fas fa-folder-plus',
                ],
            ],
        ]);
    }
}