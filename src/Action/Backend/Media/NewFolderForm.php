<?php

namespace Action\Backend\Media;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class NewFolderForm extends GenericSpacesAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $path = base64_decode($args['path']);

        try {
            $parentObject = $this->cdn->getObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ])->toArray();
        } catch (Exception $e) {
            return $response->withRedirect('/admin/media/' . $this->root . '/view?e=Parent folder not found');
        }

        return $this->render($request, $response, 'admin/entity/media/new-folder.html.twig', [
            'path'  => $path,
            'pathId'=> $args['path'],
        ]);
    }
}