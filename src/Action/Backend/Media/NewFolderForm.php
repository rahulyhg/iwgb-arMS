<?php

namespace Action\Backend\Media;

use Action\Backend\GenericLoggedInAction;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class NewFolderForm extends GenericLoggedInAction {

    use SpacesActionTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $path = base64_decode($args['path']);

        try {
            $this->cdn->getObject([
                'Bucket' => $this->settings['spaces']['bucket'],
                'Key' => $path,
            ])->toArray();
        } catch (Exception $e) {
            return $response->withRedirect('/admin/media/' . $this->getEncodedRoot() . '/view?e=Parent folder not found');
        }

        $path = str_replace(substr($this->getRoot(), 0, -1), '', $path);

        return $this->render($request, $response, 'admin/entity/media/new-folder.html.twig', [
            'path'  => $path,
            'pathId'=> $args['path'],
        ]);
    }
}