<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<?php include_once('includes/html_header.php'); ?>
	<?php
	require_once __DIR__.'/vendor/autoload.php';
	$config = parse_ini_file('app.ini');
	$entityUri = $config['entity_uri'];
	$blogtitle = $config['blogtitle'];
	$bloghost = $config['bloghost'];
	$language = $config['language'];
	if ($config['imprinturl'] !== '') {
		$imprinturl = $config['imprinturl'];
	}
	$displayed_statuses = $config['statuses_profile'];

	$clientFactory = new Depot\Api\Client\ClientFactory;
	$client = $clientFactory->create();
	$server = $client->discover($entityUri);

	$statusPostCriteria = new Depot\Core\Model\Post\PostCriteria;
	if($displayed_statuses !== '') {
			$statusPostCriteria->limit = $displayed_statuses;
		}
	else {
			$statusPostCriteria->limit = 15;
	}
	$statusPostCriteria->postTypes = array('https://tent.io/types/post/status/v0.1.0', );
	$statusListResponse = $client->posts()->getPosts($server, $statusPostCriteria);
	include_once('includes/profile.php');
	echo '<title>', $name,' - Profile</title>';
	?>
</head>

<body>
	<nav> <!-- Page navigation -->
	<?php
	echo '<a href="', $bloghost, '/" class="home"></a>';
	?>
	<a href="<?php echo $bloghost; ?>/feed/" class="feed"></a>
	<a href="profile.php" class="profile"></a>
	</nav>
	<div class="clear_head"></div>	
	<div id="container">
	<div id="main" role="main">
			<div id="profile">
				<div id="avatar"><img src="<?php echo $avatar_url; ?>" alt="Avatar" /></div>
				<div id="name"><?php echo $name; ?></div>
				<div id="clear"></div>
				<div id="information">
				<div class="information"><?php echo $gender; ?></div>
				<div class="informatio"><?php echo $location; ?></div>
				<?php if(isset($birthday)) {echo '<div class="information">', $birthday, '</div>'; } ?>
			<?php
			if (isset($website)) {
				echo '<div class="information"><a href="', $website, '">', $website, '</a></div>';
			}
			?>
			<div class="seperator"></div>
			<div id="status">
				<?php
				if ($statusListResponse->posts()) {
					foreach ($statusListResponse->posts() as $post) {
						$content = $post->content();
						if ($post->mentions() == array()) {
							echo '<div class="status"><a href="', $entityUri, '/posts/', $post->id(), '">', $content['text'], '</a></div>';
							// The line above only works if you're on tent.is. If your not, please delete the line and use the line below
							// echo '<div class="status">', $content['text'], '</div>';
						}
						
					}
				}
				?>
			</div>
		</div>
		<div class="clear_head"></div>
		</div> <!-- End profile -->
	</div>
	</div>
	<?php include_once('includes/footer.php'); ?>
</body>
</html>