<?php

namespace Action\Backend\Media;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UploadForm extends GenericSpacesAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $root = base64_decode($this->root);

        $objects = $this->cdn->listObjects([
            'Bucket' => $this->bucket,
            'Prefix' => $root,
        ])->toArray()['Contents'];

        $folders = [];
        foreach ($objects as $object) {
            if (substr($object['Key'], -1) == '/') {
                $folders[] = str_replace(substr($root, 0, -1), '', $object['Key']);
            }
        }

        return $this->render($request, $response, 'admin/entity/media/upload.html.twig', [
            'folders' => $folders,
        ]);
    }
}