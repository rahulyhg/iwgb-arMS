<?php

namespace Action\Frontend;

use Slim\Http\Request;
use Slim\Http\Response;

class Home extends GenericPublicAction {

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param string[] $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $args) {

        /* @var $postRepo \Domain\PostRepository */
        $postRepo = $this->em->getRepository('\Domain\Post');
        $pinned = $postRepo->getPinnedPost()[0];
        $stories = $postRepo->getStoriesExcluding($pinned, 3);

        return $this->render($request, $response, 'home.html.twig', [
            'header'    => $pinned,
            'stories'   => $stories,
            'branches'  => \JSONObject::getAllItems(\Config::Branches),
            'home'      => (array) new \JSONObject(\Config::Pages, 'home'),
        ]);
    }
}