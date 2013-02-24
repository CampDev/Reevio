<?php $title="Home"; ?>
<?php
/* Information about your Reevio installation
Version: 0.1
Released: 03/02/2013
License: BSD 3.0 (see LICENSE.txt)
*/
$config = parse_ini_file('app.ini');
$entityUri = $config['entity_uri'];
$blogtitle = $config['blogtitle'];
$bloghost = $config['bloghost'];
$language = $config['language'];
if ($config['imprinturl'] !== '') {
	$imprinturl = $config['imprinturl'];
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo $language; ?>">
<head>
	<script type="text/javascript">$('iframe').wrap('<div class="video" />');</script>
	<meta charset="utf-8">
	<?php include_once('includes/html_header.php'); ?>
	<title>
		<?php
		if (isset($title)) {
		echo $title, ' - ', $blogtitle;
		}
		else {
		echo $blogtitle;
		}
		?>
	</title>
		<?php
		require_once __DIR__.'/vendor/autoload.php';
		$clientFactory = new Depot\Api\Client\ClientFactory;
		$client = $clientFactory->create();
		$server = $client->discover($entityUri);

		$essayPostCriteria = new Depot\Core\Model\Post\PostCriteria;
		$essayPostCriteria->limit = 10;
		$essayPostCriteria->postTypes = array('https://tent.io/types/post/essay/v0.1.0', );

		$essayPostListResponse = $client->posts()->getPosts($server, $essayPostCriteria); 
		?>
		<script>
  $(document).ready(function(){
    // Target your .container, .wrapper, .post, etc.
    $("div").fitVids();
  });
</script>
</head>

<body>
	<nav> <!-- Page navigation -->
	<a href="<?php //echo $bloghost;?>feed/" class="feed"></a>
	<a href="profile.php" class="profile"></a>
	</nav>
	<div class="clear_head"></div>

		<div id="container">
		<div id="main" role="main">
		<?php 
		foreach ($essayPostListResponse->posts() as $post) {
			$content = $post->content();
			echo '<article>';
			if (isset($content['title'])) { 
				echo '<h1><a href="article.php?id=', $post->id(), '" title="', $content['title'], '">', $content['title'], '</a></h1>';
			}
			echo '<div class="time">', date('r', $post->publishedAt()), '</div>';
			if ($content['excerpt'] !== '') {
				echo '<p>', $content['excerpt'], '</p>';
				echo '<div class="readmore"><a href="article.php?id=',$post->id(), '" title="', $content['title'], '">Read more &rarr;</a></div>';
			}
			else
			echo '<p>', $content['body'],'</p>';
			echo '</article>';
			echo '<div class="seperator"></div>';
		}
		?>
		<div id="more_posts">Load more posts</div>
	</div>
	<?php include_once('includes/sidebar.php'); ?>
	<div class="clear"></div>
	</div>
	<?php include_once('includes/footer.php'); ?>
</body>
</html>