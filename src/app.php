<?php
$app['reevio.config'] = parse_ini_file(__DIR__.'/../app.ini');

if (!function_exists('http_parse_headers')) {
    function http_parse_headers($headers){
        if($headers === false){
            return false;
            }
        $headers = str_replace("\r","",$headers);
        $headers = explode("\n",$headers);
        foreach($headers as $value){
            $header = explode(": ",$value);
            if($header[0] && !$header[1]){
                $headerdata['status'] = $header[0];
                }
            elseif($header[0] && $header[1]){
                $headerdata[$header[0]] = $header[1];
                }
            }
        return $headerdata;
    }
}

//Function to discovery an entity's meta post
function discover_link($entity_uri, $debug){
        $entity_sub = substr($entity_uri, 0, strlen($entity_uri)-1);
        $header_result = get_headers($entity_uri);
        $discovery_link = str_replace("<", "", $header_result[2]);
		$discovery_link = str_replace(">", "", $discovery_link);
        $discovery_link = str_replace("Link: ", "", $discovery_link);
		$discovery_link = str_replace('; rel="https://tent.io/rels/meta-post"', "", $discovery_link);
       	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $entity_sub.$discovery_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $meta = json_decode(curl_exec($ch));
	    curl_close($ch);
	    if ($debug == true) {
            echo "<p><b>Entity-Sub: </b>".$entity_sub.$discovery_link."</p>";
            echo "<hr /><p><b>Header: </b></p>";
            var_dump($header_result);
            echo "<p><b>Status: ".$header_result[0]."</b></p>";
            echo "<p><b>Length: ".$header_result[1]."</b></p>";
            echo "<hr /><p><b>Discovered Link: </b></p>";
            echo "<p>".$discovery_link."</p>";
            echo "<hr /> <p><b>Meta Post: </b></p>";	
	     	var_export($meta);
	        }
	    return $meta;
}

$meta = discover_link($app['reevio.config']['entity_uri'], false);

function recent_statuses($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$post = json_decode(curl_exec($ch));
	curl_close($ch);
	return $post->posts;
}

function request_profile($profile_url) {
	$init = curl_init();
	curl_setopt($init, CURLOPT_URL, $profile_url);
	curl_setopt($init, CURLOPT_RETURNTRANSFER, true);
	$profile = json_decode(curl_exec($init));
	curl_close($init);
	return $profile;
}

$app['reevio.profile'] = $app->share(function () use ($app) {
	$profile = $meta->post->content->profile;
	return $profile;
});

$app['reevio.recent_statuses'] = $app->share(function () use ($app) {
	$status_url = $app['reevio.config']['entity_uri'].'posts?limit='.$app['reevio.config']['statuses_sidebar'].'&types=https%3A%2F%2Ftent.io%2Ftypes%2Fstatus%2Fv0%23';
	$posts = recent_statuses($status_url);

	return $posts;
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

    $twig->addGlobal('recent_statuses', $c['reevio.recent_statuses']);
    $twig->addGlobal('profile', $c['reevio.profile']);

    return $twig;
}));

$app['debug'] = true;