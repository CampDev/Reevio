<?php
header('Content-Type: text/xml');
$config = parse_ini_file('../app.ini');
$entityUri = $config['entity_uri'];
$blogtitle = $config['blogtitle'];
$bloghost = $config['bloghost'];
$language = $config['language'];
if ($config['imprinturl'] !== '') {
	$imprinturl = $config['imprinturl'];
}
$description = $config['description'];

require_once __DIR__.'/../vendor/autoload.php';
$clientFactory = new Depot\Api\Client\ClientFactory;
$client = $clientFactory->create();
$server = $client->discover($entityUri);

$essayPostCriteria = new Depot\Core\Model\Post\PostCriteria;
$essayPostCriteria->limit = 10;
$essayPostCriteria->postTypes = array('https://tent.io/types/post/essay/v0.1.0', );

$essayPostListResponse = $client->posts()->getPosts($server, $essayPostCriteria);

echo '<?xml version="1.0"?>';
echo '<rss version="2.0">';
?>
<channel> 
	<title><?php echo $blogtitle; ?></title> 
	<link><?php echo $bloghost; ?></link>
	<copyright><?php echo $entityUri; ?></copyright>
	<description><?php echo $description; ?></description>
	<language><?php echo $language; ?></language> 
	<pubDate><?php echo date('r'); ?></pubDate>
	<lastBuildDate><?php echo date('r'); ?></lastBuildDate>
	<generator>Reevio</generator>

	<?php 
		foreach ($essayPostListResponse->posts() as $post) {
			$content = $post->content();
			$post_id = $post->id();
			echo '<item>';
			if (isset($content['title'])) { 
				echo '<title>', $content['title'], '</title>';
			}
			else {
				echo 'Essay - ', $post->publishedAt();
			}
			echo '<link>', $bloghost, '/article.php?id=', $post_id ,'</link>';
			echo '<pubDate>', date('r', $post->publishedAt()) ,'</pubDate>';
			echo '<source>', $bloghost, '/feed/ </source>';

			if (isset($content['excerpt'])) {
				echo '<description>', $content['excerpt'], '</description>';
			}
			else {
				echo '<description>', $content['body'], '</description>';
			}
			echo '</item>';
		}
		?>

</channel>
</rss>