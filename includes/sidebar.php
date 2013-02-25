<?php 
require_once __DIR__.'/../vendor/autoload.php';
$config = parse_ini_file('app.ini');
$entityUri = $config['entity_uri'];
$ClientFactory = new Depot\Api\Client\ClientFactory;
$client = $ClientFactory->create();
$server = $client->discover($entityUri);
$statuses_sidebar = $config['statuses_sidebar'];

//Profile fetching
$basicProfileInfo = $server->entity()->findProfileInfo('https://tent.io/types/info/basic/v0.1.0')->content();
$avatar_url = $basicProfileInfo['avatar_url']; //Avatar URL
$name = $basicProfileInfo['name']; //Name
if (isset($basicProfileInfo['website_url'])) { $website = $basicProfileInfo['website_url']; } //Website
$birthday = $basicProfileInfo['birthdate']; //Birthday
$location = $basicProfileInfo['location']; //Location
$gender = $basicProfileInfo['gender']; //Gender
$bio = $basicProfileInfo['bio']; //Bio

//Status fetching
$statusPostCriteria = new Depot\Core\Model\Post\PostCriteria;
if($statuses_sidebar !== '') {
			$statusPostCriteria->limit = $statuses_sidebar;
		}
	else {
			$statusPostCriteria->limit = 15;
	}
$statusPostCriteria->limit = 5;
$statusPostCriteria ->postTypes = array('https://tent.io/types/post/status/v0.1.0', );
$statusListRepsonse = $client->posts()->getPosts($server, $statusPostCriteria);
?>
<aside> <!-- Sidebar -->
	<div id="avatar"><img src="<?php echo $avatar_url; ?>" /></div>
	<div id="name"><?php echo $name; ?></div>
	<div id="clear"></div>
	<div id="information">
		<div class="information"><?php echo $gender; ?></div>
		<div class="informatio"><?php echo $location; ?></div>
		<?php if(isset($birthday)) {echo '<div class="information">', $birthday, '</div>'; } ?>
		<div class="information"><a href="<?php echo $website; ?>"><?php echo $website; ?></a></div>
	</div>
	<div class="seperator"></div>
	<div id="bio"><?php echo $bio; ?></div>
	<div class="seperator"></div>
	<!--
	<div style="margin: auto; text-align:center;">
		Any buttons or information you'd like to have in your sidebar goes here (examples: Flattr button or links to your tent-profile etc.)
		But don't forget to remove the comments around the div ;)
	</div>
	<div class="seperator"></div>
	-->
	<div id="status">
		<?php
		if ($statusListRepsonse->posts()) {
			echo '<h1>Latest statuses:</h1>';
			foreach ($statusListRepsonse->posts() as $post) {
				$content = $post->content();
				if ($post->mentions() == array()) {
					echo '<div class="status"><a href="', $entityUri, '/posts/', $post->id(), '">', $content['text'], '</a></div>';
				}
			}
		}
		?>
	</div>
	<div class="clear_head"></div>
</aside>