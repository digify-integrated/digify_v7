<?php
declare(strict_types=1);

session_start();

/**
 * Ensure the user is authenticated.
 * If not, clear the session and redirect to the login page.
 */
if (isset($_SESSION['user_account_id']) && !empty($_SESSION['user_account_id'])) {
    $userID  = (int) $_SESSION['user_account_id']; // cast for safety
}
else {
    // Clear all session data
    session_unset();
    session_destroy();

    // Redirect to login
    header('Location: index.php', true, 302);
    exit;
}