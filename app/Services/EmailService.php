<?php
namespace App\Services;

class EmailService {
    /**
     * Sends a basic HTML email.
     * Note: For production, consider integrating PHPMailer or an API like SendGrid.
     */
    public static function send(string $to, string $subject, string $body): bool {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@digify.local" . "\r\n";

        // Using standard PHP mail() for beginner-friendly baseline
        return mail($to, $subject, $body, $headers);
    }
}