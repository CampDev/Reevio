<?php 
	require_once __DIR__.'/../vendor/autoload.php';
	$config = parse_ini_file('app.ini');
	$entityUri = $config['entity_uri'];
	$ClientFactory = new Depot\Api\Client\ClientFactory;
	$client = $ClientFactory->create();
	$server = $client->discover($entityUri);

	$basicProfileInfo = $server->entity()->findProfileInfo('https://tent.io/types/info/basic/v0.1.0')->content();
	$avatar_url = $basicProfileInfo['avatar_url']; //Avatar URL
	$name = $basicProfileInfo['name']; //Name
	if (isset($basicProfileInfo['website_url'])) {
		$website = $basicProfileInfo['website_url']; //Website
	}
	$birthday = $basicProfileInfo['birthdate']; //Birthday
	$location = $basicProfileInfo['location']; //Location
	$gender = $basicProfileInfo['gender']; //Gender
	$bio = $basicProfileInfo['bio']; //Bio
?>