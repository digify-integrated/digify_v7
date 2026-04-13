<?php
use App\Controllers\AuthController;
use Core\Application;

$app = new Application();

/**
 * 1. BASIC ROUTES
 */
$app->router->get('/', [AuthController::class, 'index'], [], 'auth.login');

/**
 * 2. DYNAMIC ROUTES
 * Use curly braces {id} to capture variables from the URL.
 * These are passed as arguments to your controller method.

$app->router->get('/profile/{id}', [UserController::class, 'show']);
$app->router->get('/post/{category}/{slug}', [TestController::class, 'viewPost']);
 */

/**
 * 3. GROUPED ROUTES & MIDDLEWARE
 * Grouping allows you to prefix URLs and apply Middlewares to multiple routes at once.

$app->router->group('/admin', function($router) {
    
    // All routes inside this function automatically start with /admin
    // Example: /admin/dashboard
    $router->get('/dashboard', [TestController::class, 'index']);
    
    // Example: /admin/settings
    $router->get('/settings', [TestController::class, 'settings']);

}, [AuthMiddleware::class]); // The second argument applies the middleware to the whole group
 */

/**
 * 4. API ROUTES
 * You can also group by purpose, like an API, even without middleware.

$app->router->group('/api/v1', function($router) {
    $router->get('/status', function() {
        header('Content-Type: application/json');
        return json_encode(['status' => 'Digify v7 is Online']);
    });
});
 */

return $app;