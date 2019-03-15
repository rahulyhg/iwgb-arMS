<?php

namespace Action\Backend\Media;

use Action\Backend\GenericLoggedInAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Upload extends GenericLoggedInAction {

    use SpacesActionTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        /** @var UploadedFileInterface $file */
        $file = $request->getUploadedFiles()['file'];
        $form = $request->getParsedBody();
        $root = substr($this->getRoot(), 0, -1);
        $generate = !empty($form['generate']);

        if (empty($file) ||
            $file->getError() != UPLOAD_ERR_OK ||
            empty($form['folder']) ||
            (!$generate && empty($form['filename']))) {
            return $response->withRedirect('/admin/media/new?e=The data you sent was invalid');
        }

        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $id = uniqid() . '.' . $extension;
        $path = $root . $form['folder'];
        $name = ($generate) ?
            $name = $id :
            $form['filename'] . '.' . $extension;

        if (!preg_match('/^[a-zA-Z0-9\-]+\.[a-zA-Z0-9]+$/', $name)) {
            return $response->withRedirect('/admin/media/new?e=That filename is not allowed');
        }

        $file->moveTo(APP_ROOT . '/var/upload/' . $id);

        $this->cdn->putObject([
            'Bucket' => $this->settings['spaces']['bucket'],
            'Key' => $path . $name,
            'ACL' => 'public-read',
            'ContentType' => $file->getClientMediaType(),
            'SourceFile' => APP_ROOT . '/var/upload/' . $id,
        ]);

        unlink('/var/upload/' . $id);

        return $response->withRedirect('/admin/media/' . base64_encode($path) . '/view?m=File uploaded successfully');

    }
}