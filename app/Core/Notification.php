<?php
namespace App\Core;

/**
 * Class Notification
 * Orchestrates multi-channel notification delivery.
 */
class Notification {
    /**
     * Constructor using PHP 8.x Property Promotion.
     */
    public function __construct(
        private readonly object $notificationSetting,
        private readonly object $email,
        private readonly object $sms,
        private readonly object $systemNotification
    ) {}

    /**
     * Send a notification through configured channels.
     */
    public function sendNotification(
        int $notificationSettingId,
        array|string|null $recipientEmails = [],
        array|string|null $recipientPhones = [],
        array|int|null $recipientUserIds = [],
        array $placeholders = [],
        array $ccEmails = [],
        array $bccEmails = []
    ): bool {
        $success = false;

        // 1. Fetch settings and templates in one go if possible
        $setting = $this->notificationSetting->fetchNotificationSetting($notificationSettingId);
        if (!$setting) {
            error_log("Notification Error: Setting ID {$notificationSettingId} not found.");
            return false;
        }

        // 2. Prepare Placeholders (Normalize keys for replacement)
        $search = array_map(fn($key) => "#{{$key}}", array_keys($placeholders));
        $replace = array_values($placeholders);

        // 3. System Notifications
        if ((int)$setting['system_notification'] === 1 && !empty($recipientUserIds)) {
            $template = $this->notificationSetting->fetchSystemNotificationTemplate($notificationSettingId);
            if ($template) {
                $title = str_replace($search, $replace, $template['system_notification_title']);
                $msg   = str_replace($search, $replace, $template['system_notification_message']);

                foreach ((array)$recipientUserIds as $userId) {
                    if ($this->systemNotification->send($userId, $title, $msg)) $success = true;
                }
            }
        }

        // 4. Email Notifications
        if ((int)$setting['email_notification'] === 1 && !empty($recipientEmails)) {
            $template = $this->notificationSetting->fetchEmailNotificationTemplate($notificationSettingId);
            if ($template) {
                $subject = str_replace($search, $replace, $template['email_notification_subject']);
                $body    = str_replace($search, $replace, $template['email_notification_body']);

                $result = $this->email->sendEmail((array)$recipientEmails, $subject, $body, $ccEmails, $bccEmails);
                if ($result === true) $success = true;
            }
        }

        // 5. SMS Notifications
        if ((int)$setting['sms_notification'] === 1 && !empty($recipientPhones)) {
            $template = $this->notificationSetting->fetchSmsNotificationTemplate($notificationSettingId);
            if ($template) {
                $msg = str_replace($search, $replace, $template['sms_notification_message']);

                foreach ((array)$recipientPhones as $phone) {
                    if ($this->sms->sendSms($phone, $msg)) $success = true;
                }
            }
        }

        return $success;
    }
}