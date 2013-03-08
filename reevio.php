<?php

/*
Information about your Reevio installation:
Version: 0.1
Release Date: 03/09/2013
License: BSD 3.0 (see LICENSE.txt)
*/

require __DIR__.'/vendor/autoload.php';

$app = new Silex\Application;

require __DIR__.'/src/app.php';
require __DIR__.'/src/controllers.php';

$app->run();
