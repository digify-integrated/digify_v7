<?php

namespace App\Helpers;

use App\Core\Security;
use DateTime;
use Exception;

class SystemHelper {
    
    /**
     * Returns a human-readable elapsed time string.
     * Fixed logic to handle future dates and improve precision.
     */
    public static function timeElapsedString(string $dateTime): string {
        try {
            $prev = new DateTime($dateTime);
            $now  = new DateTime();
            $diff = $now->diff($prev);

            if ($now < $prev) return 'In the future';
            
            // If older than 24 hours, return formatted date
            if ($diff->days >= 1) {
                return $prev->format('M j, Y \a\t h:i:s A');
            }

            $intervals = [
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            ];

            foreach ($intervals as $prop => $label) {
                if ($diff->$prop > 0) {
                    return $diff->$prop . " {$label}" . ($diff->$prop > 1 ? 's' : '') . " ago";
                }
            }

            return 'Just Now';
        } catch (Exception $e) {
            return 'Invalid date';
        }
    }

    /**
     * Send standardized JSON responses (Success/Error).
     * Great for your AJAX front-end!
     */
    public static function sendResponse(bool $success, string $title, string $message, array $extra = []): void {
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'success'      => $success,
            'title'        => $title,
            'message'      => $message,
            'message_type' => $success ? 'success' : 'error',
        ], $extra));
        exit;
    }

    /**
     * Check if a file exists using absolute paths.
     */
    public static function checkFileExists(string $path): bool {
        $basePath = defined('PROJECT_BASE_DIR') ? PROJECT_BASE_DIR : dirname(__DIR__, 2);
        $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim($path, './');
        return file_exists($fullPath) && is_file($fullPath);
    }

    /**
     * Map file extension to icon.
     */
    public static function getFileIcon(string $filename): string {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $iconMap = [
            'pdf'  => 'pdf.svg',
            'doc'  => 'doc.svg', 'docx' => 'doc.svg',
            'xls'  => 'xls.svg', 'xlsx' => 'xls.svg',
            'jpg'  => 'img.svg', 'png'  => 'img.svg', 'jpeg' => 'img.svg',
            'zip'  => 'rar.svg', 'rar'  => 'rar.svg',
        ];

        $icon = $iconMap[$ext] ?? 'default.svg';
        return "./assets/images/file_icon/img-file-{$icon}";
    }
}