<?php

namespace Action\Frontend;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Home extends GenericPublicAction {

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface {

        /* @var $postRepo \Domain\PostRepository */
        $postRepo = $this->em->getRepository(\Domain\Post::class);
        $pinned = $postRepo->getPinnedPost()[0];
        $stories = $postRepo->getStoriesExcluding($pinned, 3);

        return $this->render($request, $response, 'home.html.twig', [
            'pinned'    => $pinned,
            'stories'   => $stories,
            'branches'  => \JSONObject::getAll(\Config::Branches),
            'elements'  => \JSONObject::get(\Config::Pages, 'home')['elements'],
        ]);
    }
}