<?php

namespace Action\Backend\Media;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class NewFolder extends GenericSpacesAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $form = $request->getParsedBody();
        $root = substr(base64_decode($this->root), 0, -1);

        if (empty($form['folderName']) ||
            empty($form['path']) ||
            !preg_match('/^[a-zA-Z0-9\-]*$/', $form['folderName'])) {
            return $response->withRedirect('/admin/media/' . $this->root . '/view?e=The data you sent was invalid');
        }

        try {
            $this->cdn->getObject([
                'Bucket' => $this->bucket,
                'Key' => $root . $form['path'],
            ])->toArray();
        } catch (Exception $e) {
            return $response->withRedirect('/admin/media/' . $this->root . '/view?e=Parent folder not found');
        }

        $path = $root . $form['path'] . $form['folderName'] . '/';

        $this->cdn->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'ACL' => 'public-read',
        ]);

        return $response->withRedirect('/admin/media/' . base64_encode($path) . '/view?m=Folder created successfully');

    }
}