<?php

namespace Action\Backend\Media;

use Action\Backend\GenericLoggedInAction;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class NewFolder extends GenericLoggedInAction {

    use SpacesActionTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $form = $request->getParsedBody();
        $root = substr($this->getRoot(), 0, -1);

        if (empty($form['folderName']) ||
            empty($form['path']) ||
            !preg_match('/^[a-zA-Z0-9\-]*$/', $form['folderName'])) {
            return $response->withRedirect('/admin/media/' . $this->getEncodedRoot() . '/view?e=The data you sent was invalid');
        }

        try {
            $this->cdn->getObject([
                'Bucket' => $this->settings['spaces']['bucket'],
                'Key' => $root . $form['path'],
            ])->toArray();
        } catch (Exception $e) {
            return $response->withRedirect('/admin/media/' . $this->getEncodedRoot() . '/view?e=Parent folder not found');
        }

        $path = $root . $form['path'] . $form['folderName'] . '/';

        $this->cdn->putObject([
            'Bucket' => $this->settings['spaces']['bucket'],
            'Key' => $path,
            'ACL' => 'public-read',
        ]);

        return $response->withRedirect('/admin/media/' . base64_encode($path) . '/view?m=Folder created successfully');

    }
}