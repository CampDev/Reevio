<?php if (!isset($_GET['id'])) {
	header('Location: index.php');
}
$config = parse_ini_file('app.ini');
$entityUri = $config['entity_uri'];
$blogtitle = $config['blogtitle'];
$bloghost = $config['bloghost'];
$language = $config['language'];
if ($config['imprinturl'] !== '') {
	$imprinturl = $config['imprinturl'];
}

require_once __DIR__.'/vendor/autoload.php';
$clientFactory = new Depot\Api\Client\ClientFactory;
$client = $clientFactory->create();
$server = $client->discover($entityUri);

$essayPostCriteria = new Depot\Core\Model\Post\PostCriteria;
$essayPostCriteria->limit = 1;
$essayPostCriteria->postTypes = array('https://tent.io/types/post/essay/v0.1.0', );
$post = $client->posts()->getPost($server, $_GET['id']);
$content = $post->content();
if (isset($content['title'])) {
	$title = $content['title'];
} 
include_once('includes/globals.php'); 
?>
<!DOCTYPE HTML>
<html lang="<?php echo $language; ?>">
<head>
	<meta charset="utf-8">
	<?php include_once('includes/html_header.php'); ?>
	<title>
		<?php
		if (isset($title)) { echo $title, ' - ', $blogtitle; }
		else { echo $blogtitle; }
		?>
	</title>
</head>

<body>
	<nav> <!-- Page navigation -->
	<?php
	echo '<a href="', $bloghost, '" class="home"></a>';
	?>
	<a href="<?php //echo $bloghost;?>feed/" class="feed"></a>
	<a href="profile.php" class="profile"></a>
	</nav>
	<div class="clear_head"></div>
	<div id="container">
	<div id="main" role="main">
		<?php
		echo '<article>';
		if (isset($content['title'])) { 
			echo '<h1>', $content['title'], '</h1>';
		}
		echo '<div class="time">', date('r', $post->publishedAt()), '</div>';
		echo '<div class="seperator"></div>';
		echo '<p>', $content['body'],'</p>';
		if (isset($content['tags'])) {
			echo '<div class="seperator"></div>';
			echo '<div id="tags">', $content['tags'], '</div>';
		}
		?>
		<!--
		<div style="margin: auto; text-align:center;">
			Any buttons or information you'd like to have below every article goes here (example: Flattr button)
			But don't forget to remove the comments around the div ;)
		</div> 
		-->
	</div>
	<?php include_once('includes/sidebar.php'); ?>
	<div class="clear"></div>
	</div>
	<?php include_once('includes/footer.php'); ?>
</body>
</html>