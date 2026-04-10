<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    /**
     * Send an email.
     * * @return bool Returns true on success, false on failure (error is logged).
     */
    public function sendEmail(
        string|array $toEmail,
        string $subject,
        string $body,
        array $cc   = [],
        array $bcc  = []
    ): bool {
        $mailer = new PHPMailer(true);

        try {
            // 1. Configure SMTP
            $mailer->isSMTP();
            $mailer->isHTML(true);
            $mailer->CharSet    = 'UTF-8'; // Prevent character encoding issues
            $mailer->Host       = MAIL_SMTP_SERVER;
            $mailer->SMTPAuth   = MAIL_SMTP_AUTH;
            $mailer->Username   = MAIL_USERNAME;
            $mailer->Password   = MAIL_PASSWORD;
            $mailer->SMTPSecure = MAIL_SMTP_SECURE;
            $mailer->Port       = MAIL_SMTP_PORT;

            // 2. Set Sender
            $mailer->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);

            // 3. Add Recipients
            foreach ((array) $toEmail as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mailer->addAddress($email);
                }
            }

            foreach ($cc as $email) $mailer->addCC($email);
            foreach ($bcc as $email) $mailer->addBCC($email);

            // 4. Content
            $fullHtmlBody    = $this->applyTemplate($subject, $body);
            $mailer->Subject = $subject;
            $mailer->Body    = $fullHtmlBody;
            
            // Plain text version for spam filters and accessibility
            $mailer->AltBody = strip_tags($body);

            return $mailer->send();
        } catch (Exception $e) {
            // Log the error for the developer, don't leak SMTP details to the user
            error_log("PHPMailer Error: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Injects content into the HTML wrapper.
     */
    private function applyTemplate(string $subject, string $body): string {
        $templatePath = __DIR__ . '/template/email-template.html';

        if (file_exists($templatePath)) {
            $template = file_get_contents($templatePath);
            return str_replace(
                ['{EMAIL_SUBJECT}', '{EMAIL_CONTENT}'],
                [$subject, $body],
                $template
            );
        }

        return $body;
    }
}