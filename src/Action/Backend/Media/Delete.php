<?php

namespace Action\Backend\Media;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Delete extends GenericSpacesAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $path = base64_decode($args['path']);

        try {
            $this->cdn->getObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ])->toArray();
        } catch (Exception $e) {
            return $response->withRedirect('/admin/media/' . $this->root . '/view?e=Object not found');
        }

        $parent = $parent = substr($path, 0,
            strrpos($path, '/', -2) + 1);

        $name = substr($path, strlen($parent));

        if (!$request->getQueryParam('confirm')) {
            return $response->withRedirect('/admin/media/' . base64_encode($parent) . '/view?w=Are you sure you want to delete ' . $name . '%3F&c=/admin/media/' . $args['path'] . '/delete?confirm=1');
        }

        $this->cdn->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
        ]);

        return $response->withRedirect('/admin/media/' . base64_encode($parent) . '/view?m=Deleted successfully');
    }
}