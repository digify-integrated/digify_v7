<?php

namespace App\Core;

use RuntimeException;

class Security {
    // Ensure this key is exactly 32 bytes for AES-256
    private const PEPPER = ENCRYPTION_KEY;

    /**
     * Hash a token with a pepper for extra security.
     */
    public static function hashToken(string $token): string {
        $peppered = hash_hmac('sha256', $token, self::PEPPER);
        return password_hash($peppered, PASSWORD_DEFAULT);
    }

    public static function verifyToken(string $token, string $storedHash): bool {
        $peppered = hash_hmac('sha256', $token, self::PEPPER);
        return password_verify($peppered, $storedHash);
    }

    /**
     * Encrypt data using AES-256-GCM (Authenticated Encryption).
     * This provides both confidentiality AND integrity.
     */
    public static function encryptData(string $plainText): string|false {
        if (empty(trim($plainText))) return false;

        $ivLength = openssl_cipher_iv_length('aes-256-gcm');
        $iv = random_bytes($ivLength);
        
        // GCM requires a tag to verify data integrity
        $ciphertext = openssl_encrypt(
            $plainText,
            'aes-256-gcm',
            self::PEPPER,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false) return false;

        // Store IV + Tag + Ciphertext
        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * Decrypt data and verify integrity tag.
     */
    public static function decryptData(string $encryptedBase64): string|false {
        $data = base64_decode($encryptedBase64, true);
        if (!$data) return false;

        $ivLen = openssl_cipher_iv_length('aes-256-gcm');
        $tagLen = 16; // Standard tag length for GCM
        
        $iv = substr($data, 0, $ivLen);
        $tag = substr($data, $ivLen, $tagLen);
        $ciphertext = substr($data, $ivLen + $tagLen);

        return openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            self::PEPPER,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        ) ?: false;
    }

    /**
     * CSRF Validation: Uses hash_equals to prevent timing attacks.
     */
    public static function validateCSRFToken(?string $token, string $formKey = 'default'): bool {
        if (empty($token)) return false;
        
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $stored = $_SESSION['csrf_tokens'][$formKey] ?? null;
        if (!$stored) return false;

        return hash_equals($stored, $token);
    }

    /**
     * Helper to get or create a token (useful for AJAX headers)
     */
    public static function getCSRFToken(string $formKey = 'default'): string {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        if (!isset($_SESSION['csrf_tokens'][$formKey])) {
            $_SESSION['csrf_tokens'][$formKey] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_tokens'][$formKey];
    }

    /**
     * Securely generates a random filename.
     */
    public static function generateFileName(int $bytes = 16): string {
        return bin2hex(random_bytes($bytes));
    }

     /**
     * Obscure an email address for display/logging (e.g. j***e@ex*****.com).
     *
     * - Masks most of the username but keeps first/last character (if length > 2).
     * - Masks part of the domain (leaving TLD visible).
     * - Handles short usernames safely.
     * - Returns "invalid email" placeholder if input is not a valid email.
     *
     * @param string $email Email address
     * @return string Masked email
     */
    public static function obscureEmail(string $email) {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '[invalid email]';
        }

        [$username, $domain] = explode('@', strtolower(trim($email)), 2);

        // --- Mask username ---
        if (strlen($username) <= 2) {
            // If username is too short, mask entirely
            $maskedUsername = str_repeat('*', strlen($username));
        } else {
            $maskedUsername = substr($username, 0, 1)
                . str_repeat('*', strlen($username) - 2)
                . substr($username, -1);
        }

        // --- Mask domain (keep TLD visible) ---
        $domainParts    = explode('.', $domain);
        $tld            = array_pop($domainParts);
        $maskedDomain   = '';

        foreach ($domainParts as $part) {
            $maskedDomain .= substr($part, 0, 2) . str_repeat('*', max(0, strlen($part) - 2)) . '.';
        }
        $maskedDomain .= $tld;

        return $maskedUsername . '@' . $maskedDomain;
    }

    /**
     * Obscure a card number (e.g. **** **** **** 1234).
     *
     * @param string $cardNumber Credit/debit card number
     * @return string Masked card number
     */
    public static function obscureCardNumber(string $cardNumber) {
        $last4Digits    = substr($cardNumber, -4);
        $masked         = str_repeat('*', max(0, strlen($cardNumber) - 4));
        $maskedGrouped  = implode(' ', str_split($masked, 4));

        return trim($maskedGrouped . ' ' . $last4Digits);
    }
}