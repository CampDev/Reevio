<?php

$app['reevio.config'] = parse_ini_file(__DIR__.'/../app.ini');

$app['depot.client_factory'] = $app->share(function () {
    return new Depot\Api\Client\ClientFactory;
});

$app['depot.client'] = $app->share(function () use ($app) {
    return $app['depot.client_factory']->create();
});

$app['depot.server'] = $app->share(function () use ($app) {
    return $app['depot.client']->discover($app['reevio.config']['entity_uri']);
});

$app['depot.profile.basic'] = $app->share(function () use ($app) {
    return $app['depot.server']->entity()->findProfileInfo(
        'https://tent.io/types/info/basic/v0.1.0'
    );
});

$app['depot.recent_statuses'] = $app->share(function () use ($app) {
    $postCriteria = new Depot\Core\Model\Post\PostCriteria;
    $postCriteria->postTypes = array('https://tent.io/types/post/status/v0.1.0');
    $postCriteria->limit = $app['reevio.config']['statuses_sidebar'] ?: 15;

    $statusListRepsonse = $app['depot.client']->posts()->getPosts(
        $app['depot.server'],
        $postCriteria
    );

    return $statusListRepsonse->posts();
});

$app->register(new Silex\Provider\UrlGeneratorServiceProvider);

$app->register(new Silex\Provider\TwigServiceProvider, array(
    'twig.path' => array(
        __DIR__.'/../views/custom/',
        __DIR__.'/../views/core/',
    ),
));

$app['twig'] = $app->share($app->extend('twig', function ($twig, $c) {
    $twig->addGlobal('reevio', $c['reevio.config']);

    $twig->addGlobal('recent_statuses', $c['depot.recent_statuses']);
    $twig->addGlobal('profile', array('basic' => $c['depot.profile.basic']->content()));

    return $twig;
}));
