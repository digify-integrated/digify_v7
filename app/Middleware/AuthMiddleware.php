<?php
namespace App\Middleware;

use Core\MiddlewareInterface;
use Core\Request;

class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request, \Closure $next) {
        // Simple check: if no user session exists, redirect to login
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // If authenticated, proceed to the next step (the controller)
        return $next($request);
    }
}