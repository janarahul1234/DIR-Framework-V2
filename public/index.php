<?php

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/app/core/services.php';
require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/Routes/web.php';

use App\core\Application;

$app = new Application();
$app->run();