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
function discover_link($entity_uri){
        $entity_sub = substr($entity_uri, 0, strlen($entity_uri)-1);
        $header_result = get_headers($entity_uri);
        $discovery_link = str_replace("<", "", $header_result[13]);
        $discovery_link = str_replace(">", "", $discovery_link);
        $discovery_link = str_replace("Link: ", "", $discovery_link);
        $discovery_link = str_replace('; rel="https://tent.io/rels/meta-post"', "", $discovery_link);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $entity_sub.$discovery_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $meta = json_decode(curl_exec($ch));
        curl_close($ch);
        return $meta;
}

function recent_statuses($url) {
	$ch = curl_init();
    $log = fopen('status_log.txt', 'w');
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_STDERR, $log);
	$posts = json_decode(curl_exec($ch));
	curl_close($ch);
    fclose($log);
	return $posts->posts;
}

function request_profile($entity_uri) {
    $profile = discover_link($entity_uri)->post->content->profile;
    return $profile;
}

function request_avatar($entity_uri) {
    $entity_sub = substr($entity_uri, 0, strlen($entity_uri)-1);
    $avatar_endpoint = discover_link($entity_uri)->post->content->servers[0]->urls->post_attachment;
    $avatar_endpoint = str_replace("{entity}", urlencode($entity_sub), $avatar_endpoint);
    $avatar_endpoint = str_replace("{post}", "meta", $avatar_endpoint);
    $avatar_endpoint = str_replace("{name}", discover_link($entity_uri)->post->attachments[0]->name, $avatar_endpoint);
    $avatar_url = get_headers($avatar_endpoint);
    $avatar = $entity_sub.str_replace('Location: ','', $avatar_url[5]);
    return $avatar;
}

$meta = discover_link($app['reevio.config']['entity_uri']);

$app['posts_feed'] = $meta->post->content->servers[0]->urls->posts_feed;

$app['reevio.profile'] = $app->share(function () use ($app) {
    $meta = discover_link($app['reevio.config']['entity_uri'], false);
	$profile = $meta->post->content->profile;
	return $profile;
});

$app['reevio.avatar'] = $app->share(function () use($app) {
    $avatar = request_avatar($app['reevio.config']['entity_uri']);
    return $avatar;
});

$app['reevio.recent_statuses'] = $app->share(function () use ($app) {
	$status_url = $app['posts_feed'].'?types=https%3A%2F%2Ftent.io%2Ftypes%2Fstatus%2Fv0%23&limit='.$app['reevio.config']['statuses_sidebar'];
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

    $tent_markdown = new Twig_SimpleFunction('tent_markdown', function($status) {
        $markdown_status = preg_replace("/\*(.*)\*/", "<b>$1</b>", $status); 
        $markdown_status = preg_replace("/\_(.*)\_/", "<em>$1</em>", $markdown_status);
        $markdown_status = preg_replace("/\~(.*)\~/", "<del>$1</del>", $markdown_status);
        $markdown_status = preg_replace("/\`(.*)\`/", "<code>$1</code>", $markdown_status);
        $markdown_status = preg_replace("/\[(.*)\]\((.*)\)/", "<a href='$2'>$1</a>", $markdown_status);
        return $markdown_status;
    },array('is_safe' => array('html')));
    
    $twig->addFunction($tent_markdown);

    $twig->addGlobal('reevio', $c['reevio.config']);

    $twig->addGlobal('recent_statuses', $c['reevio.recent_statuses']);
    $twig->addGlobal('profile', $c['reevio.profile']);
    $twig->addGlobal('avatar', $c['reevio.avatar']);

    return $twig;
}));

$app['debug'] = true;