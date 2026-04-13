<?php
namespace Core;

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->generateCsrfToken();
    }

    /**
     * Generates a CSRF token if one doesn't exist.
     */
    private function generateCsrfToken(): void {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function getCsrfToken(): string {
        return $_SESSION['csrf_token'];
    }

    public function verifyCsrfToken($token): bool {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}