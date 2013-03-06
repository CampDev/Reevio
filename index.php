<?php

require __DIR__.'/vendor/autoload.php';

$app = new Silex\Application;

require __DIR__.'/app.php';
require __DIR__.'/controllers.php';

$app->run();
