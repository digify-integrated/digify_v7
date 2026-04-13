<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\DotEnv;

// Load environment variables
DotEnv::load(__DIR__ . '/../.env');

// Load routes and run the app
$app = require_once __DIR__ . '/../routes/web.php';
$app->run();