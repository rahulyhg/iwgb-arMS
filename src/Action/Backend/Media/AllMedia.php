<?php

namespace Action\Backend\Media;

use Action\Backend\EntityListTrait;
use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class AllMedia extends GenericSpacesAction {

    use EntityListTrait;

    public function __construct(Container $c) {
        parent::__construct($c);
        self::addEntityListFunctions($this->view->getEnvironment());
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $folder = $request->getQueryParam('folder');

        $params = [
            'Bucket' => $this->bucket,
            'Prefix' => $folder ?? '',
        ];

        if (!empty($request->getQueryParam('folder'))) {
            $params['Prefix'] = $request->getQueryParam('folder');
        }

        $objects = $this->cdn->listObjects($params)->toArray();

        $prefixMask = 'bucket/' . $params['Prefix'];

        $folders = [];
        $entities = [];
        foreach ($objects['Contents'] as $object) {
            if (preg_match("/^bucket\/[a-zA-Z0-9_.-]+\/?$/", $object['Key'])) {
                $type = '';
                if (substr($object['Key'], -1) == '/') {
                    $type = 'folders';
                } else {
                    $type = 'entities';
                }
                ${$type}[] = [
                    'key' => $object['Key'],
                    'name' => str_replace($prefixMask, '', $object['Key']),
                    'modified' => $object['LastModified']->__toString(),
                    'type' => $type,
                ];
            }
        }

        $objects = array_merge($folders, $entities);

        return $this->render($request, $response, 'admin/entity-list.html.twig', [
            'entityName'    => 'media',
            'entityPlural'  => 'Storage',
            'entities'      => $objects,
            'columns'       => [
                'name' => 'key',
                'modified' => 'modified',
                'actions' => 'nosort',
            ],
            'page'          => $args['page'],
            'subnav'        => [
                [
                    'display'   => 'Upload',
                    'href'      => '/admin/media/new',
                    'icon'      => 'fas fa-cloud-upload-alt',
                ],
                [
                    'display'   => 'Set up on my device',
                    'href'      => '/admin/media/setup',
                    'icon'      => 'fas fa-download',
                ],
            ],
        ]);
    }
}