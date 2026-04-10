<?php
namespace App\Services;

use App\Core\Model;

/**
 * SystemNotificationService
 * Handles persistence and retrieval of in-app notifications.
 */
class SystemNotificationService extends Model
{
    /**
     * Send/Insert a new system notification.
     * Aligned with the naming expected by Core\Notification.
     */
    public function send(int $userId, string $title, string $message): bool 
    {
        $sql = 'CALL insertSystemNotification(:p_user_account_id, :p_title, :p_message)';
        
        try {
            $this->query($sql, [
                'p_user_account_id' => $userId,
                'p_title'           => $title,
                'p_message'         => $message
            ]);
            return true;
        } catch (\Exception $e) {
            error_log("SystemNotificationService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Alias for backward compatibility or explicit database naming.
     */
    public function insertSystemNotification($userId, $title, $message) 
    {
        return $this->send((int)$userId, $title, $message);
    }

    /**
     * Fetch all system notifications for a specific user.
     */
    public function fetchUserNotifications(int $userId): array 
    {
        $sql = 'CALL fetchUserNotifications(:p_user_account_id)';
        
        // Note: fetchAll() needs to be defined in your base Model.php 
        // using $stmt->fetchAll() after execution.
        $stmt = $this->query($sql, ['p_user_account_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}