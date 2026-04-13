<?php
namespace App\Controllers;

use Core\View;

class HomeController {
    public function index() {
        // Example data passing to the view
        $data = ['title' => 'Welcome to Digify_v7'];
        return View::render('home', $data);
    }
}