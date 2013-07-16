<?php
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
 
function request_posts($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$post = json_decode(curl_exec($ch));
	curl_close($ch);
	return $post;
}
 
$app->get('/', function () use ($app) {
	$req_url = $app['reevio.config']['entity_uri'].'posts?types=https://tent.io/types/essay/v0&limit='.$app['reevio.config']['displayed_essays'];
	$post = request_posts($req_url);
 
	return $app['twig']->render('index.twig', array(
        'posts' => $post->posts,
    ));
})->bind('index');
 
//Single-Post
$app->get('/post/{id}', function ($id) use ($app) {
	$entity_url = urlencode(substr($app['reevio.config']['entity_uri'], 0, strlen($app['reevio.config']['entity_uri'])-1));
	$req_url = $app['reevio.config']['entity_uri'].'posts/'.$entity_url.'/'.$id;
	$post = request_posts($req_url);
	return $app['twig']->render('post.twig', array(
        'post' => $post->post,
    ));
})->bind('post');
 
//RSS
$app->get('/rss.xml', function () use ($app) {
	$req_url = $app['reevio.config']['server_uri'].'posts?post_types=https://tent.io/types/post/essay/v0.1.0';
	$post = request_posts($req_url);
 
    $response = new Response($app['twig']->render('rss.twig', array(
        'posts' => $post,
    )));
     
$response->headers->set('Content-type', 'text/xml');
 
    return $response;
})->bind('feed');