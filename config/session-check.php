<?php
declare(strict_types=1);

session_start();

// Redirect logged-in users away from the login page
if (isset($_SESSION['user_account_id']) && !empty($_SESSION['user_account_id'])) {
    header('Location: apps.php', true, 302);
    exit;
}