<?php
namespace App\Controllers;

use Core\View;
use Core\Request;
use Core\Validator;
use App\Models\User; // We will use the users table for DB testing

class TestController {
    // Test 1: Basic View Rendering & Blade Syntax
    public function index() {
        return View::render('test/test_form', ['title' => 'Digify Test Suite', 'message' => 'Views are working!']);
    }

    // Test 2: Dynamic Route Parameters
    public function showUser(Request $request, $id) {
        return "Dynamic Route Success! You requested User ID: " . htmlspecialchars($id);
    }

    // Test 3: POST Request & Validation
    public function submitForm(Request $request) {
        $validator = new Validator();
        
        // Let's require an email and a name
        $isValid = $validator->validate($request->getBody(), [
            'name' => ['required'],
            'email' => ['required', 'email']
        ]);

        if (!$isValid) {
            // Return back to the form with errors
            return View::render('test_form', [
                'title' => 'Validation Failed',
                'errors' => $validator->getErrors()
            ]);
        }

        return "Validation Passed! Welcome, " . htmlspecialchars($request->getBody()['name']);
    }

    // Test 4: Database Active Record Test
    public function testDatabase() {
        // Ensure you migrated the 'users' table or change this to 'sample_table' 
        // if you want to test with the table we just made!
        try {
            $count = \Core\Database::getConnection()->query("SELECT count(*) FROM migrations")->fetchColumn();
            return "Database connection is active! You have {$count} migrations recorded.";
        } catch (\Exception $e) {
            return "DB Error: " . $e->getMessage();
        }
    }

    public function finalTest() {
        return View::render('test.final_test', [
            'title' => 'The Big Test',
            'safe_string' => 'This is <script>alert("XSS")</script> escaped.',
            'html_string' => '<strong>This is raw HTML</strong>'
        ], 'main/main2');
    }
}