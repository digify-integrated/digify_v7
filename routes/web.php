<?php
use App\Controllers\HomeController;
use Core\Application;

$app = new Application();

// Static route returning a view
$app->router->get('/', [HomeController::class, 'index']);

// Dynamic route example
$app->router->get('/users/{id}', function($id) {
    return "Displaying profile for user ID: " . $id;
});

return $app;