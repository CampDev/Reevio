<?php
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
 
function request_posts($url) {
	$log = fopen('request_post_log.txt', 'w');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_STDERR, $log);
	$post = json_decode(curl_exec($ch));
	curl_close($ch);
	fclose($log);
	return $post;
}
 
$app->get('/', function () use ($app) {
	$log = fopen('request.txt', 'a');
	$entity_uri = $app['reevio.config']['entity_uri'];
	$entity_sub = substr($entity_uri, 0, strlen($entity_uri)-1);
    $header_result = get_headers($entity_uri);
    $discovery_link = str_replace("<", "", $header_result[13]);
    $discovery_link = str_replace(">", "", $discovery_link);
    $discovery_link = str_replace("Link: ", "", $discovery_link);
    $discovery_link = str_replace('; rel="https://tent.io/rels/meta-post"', "", $discovery_link);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $entity_sub.$discovery_link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_STDERR, $log);
    $meta = json_decode(curl_exec($ch));
    curl_close($ch);
	$meta = discover_link($app['reevio.config']['entity_uri']);
	$posts_feed = $meta->post->content->servers[0]->urls->posts_feed;
	$req_url = $posts_feed.'?types=https%3A%2F%2Ftent.io%2Ftypes%2Fessay%2Fv0&limit='.$app['reevio.config']['displayed_essays'];
	$post = request_posts($req_url);
 	fclose($log);

	return $app['twig']->render('index.twig', array(
        'posts' => $post->posts,
    ));
})->bind('index');
 
//Single-Post
$app->get('/post/{id}', function ($id) use ($app) {
	$entity_url = urlencode(substr($app['reevio.config']['entity_uri'], 0, strlen($app['reevio.config']['entity_uri'])-1));
	$req_url = 'https://cacauu.cupcake.is/posts/'.$entity_url.'/'.$id;
	$post = request_posts($req_url);
	return $app['twig']->render('post.twig', array(
        'post' => $post->post,
    ));
})->bind('post');

// $app->get('/post/{id}', function ($id) use ($app) {
// 	$entity_url = urlencode(substr($app['reevio.config']['entity_uri'], 0, strlen($app['reevio.config']['entity_uri'])-1));
// 	$req_url = 'https://cacauu.cupcake.is/posts/'.$entity_url.'/'.$id;
// 	$post = request_posts($req_url);
// 	return $id;
// });
 
//RSS
$app->get('/rss.xml', function () use ($app) {
	$req_url = $app['reevio.config']['entity_uri'].'posts?types=https%3A%2F%2Ftent.io%2Ftypes%2Fessay%2Fv0';
	$post = request_posts($req_url);
 
    $response = new Response($app['twig']->render('rss.twig', array(
        'posts' => $post->posts,
    )));
     
$response->headers->set('Content-type', 'text/xml');
 
    return $response;
})->bind('feed');