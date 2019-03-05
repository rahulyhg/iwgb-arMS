<?php

namespace Action\Backend\Media;

use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UploadForm extends GenericLoggedInAction {

    use SpacesActionTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $objects = $this->cdn->listObjects([
            'Bucket' => $this->settings['spaces']['bucket'],
            'Prefix' => $this->getRoot(),
        ])->toArray()['Contents'];

        $folders = [];
        foreach ($objects as $object) {
            if (substr($object['Key'], -1) == '/') {
                $folders[] = str_replace(substr($this->getRoot(), 0, -1), '', $object['Key']);
            }
        }

        return $this->render($request, $response, 'admin/entity/media/upload.html.twig', [
            'folders' => $folders,
        ]);
    }
}