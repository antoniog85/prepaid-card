<?php

use Slim\App;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/config.php';

$app = new App(['settings' => $config]);

$container = $app->getContainer();

require __DIR__ . '/../app/providers.php';
require __DIR__ . '/../app/routes.php';

$app->run();
