<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->get('/', function (Request $request) use ($app) {
    $postCriteria = new Depot\Core\Model\Post\PostCriteria;
    $postCriteria->limit = isset($app['reevio.config']['displayed_essays'])
        ? $app['reevio.config']['displayed_essays']
        : 10;
    $postCriteria->postTypes = array('https://tent.io/types/post/essay/v0.1.0');

    $paginated = false;

    if ($request->query->get('since')) {
        $paginated = true;
        $postCriteria->sinceId = $request->query->get('since');
        $postCriteria->sinceIdEntity = $app['reevio.config']['entity_uri'];
    }

    if ($request->query->get('before')) {
        $paginated = true;
        $postCriteria->beforeId = $request->query->get('before');
        $postCriteria->beforeIdEntity = $app['reevio.config']['entity_uri'];
    }

    $postListResponse = $app['depot.client']->posts()->getPosts(
        $app['depot.server'],
        $postCriteria
    );

    $since = null;
    if ($postListResponse->previousCriteria()) {
        $previousPostListResponse = $app['depot.client']->posts()->getPosts(
            $app['depot.server'],
            $postListResponse->previousCriteria()
        );
        if ($previousPostListResponse->posts()) {
            $since = $postListResponse->previousCriteria()->sinceId;
        }
    }

    $before = null;
    if ($postListResponse->nextCriteria()) {
        $nextPostListResponse = $app['depot.client']->posts()->getPosts(
            $app['depot.server'],
            $postListResponse->nextCriteria()
        );
        if ($nextPostListResponse->posts()) {
            $before = $postListResponse->nextCriteria()->beforeId;
        }
    }

    return $app['twig']->render('index.twig', array(
        'posts' => $postListResponse->posts(),
        'since' => $since,
        'before' => $before,
    ));
})->bind('index');

$app->get('/post/{id}', function ($id) use ($app) {
    $post = $app['depot.client']->posts()->getPost(
        $app['depot.server'],
        $id
    );

    return $app['twig']->render('post.twig', array(
        'post' => $post,
    ));
})->bind('post');

$app->get('/rss.xml', function () use ($app) {
    $postCriteria = new Depot\Core\Model\Post\PostCriteria;
    $postCriteria->limit = $app['reevio.config']['displayed_essays'] ?: 10;
    $postCriteria->postTypes = array('https://tent.io/types/post/essay/v0.1.0');

    $postListResponse = $app['depot.client']->posts()->getPosts(
        $app['depot.server'],
        $postCriteria
    );

    $response = new Response($app['twig']->render('rss.twig', array(
        'posts' => $postListResponse->posts(),
    )));

    $response->headers->set('Content-type', 'text/xml');

    return $response;
})->bind('feed');
