<?php

namespace Action\Frontend\Tools\Roopal;

use Action\Frontend\GenericMemberAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Upload extends GenericMemberAction {

    use ParseTrait;

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $files = $request->getUploadedFiles()['file'];
        $uri = $this->parseInvoices($files);
        return $response->withRedirect($uri);
    }
}