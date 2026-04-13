<?php
namespace App\Controllers;

use Core\View;

class AuthController {
    public function index() {
        return View::render('page/auth/login', ['title' => $_ENV['APP_NAME']], 'auth');
    }
}
