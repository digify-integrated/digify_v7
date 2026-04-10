<?php
namespace App\Services;

use Exception;

/**
 * SmsService
 * Wrapper for SMS gateway integration.
 */
class SmsService
{
    /**
     * Send an SMS message.
     * * @param string $phone Recipient phone number
     * @param string $message Message content
     * @return bool True if success, false if failed (errors are logged)
     */
    public function sendSms(string $phone, string $message): bool 
    {
        $phone = $this->formatPhoneNumber($phone);
        
        if (empty($phone)) {
            error_log("SmsService Error: Invalid phone number provided.");
            return false;
        }

        try {
            /**
             * TODO: Implementation Example (Infobip/Twilio)
             * * $response = $this->gateway->send($phone, $message);
             * if (!$response->isSuccess()) {
             * throw new Exception($response->getErrorMessage());
             * }
             */

            // Simulation of a successful API call
            return true; 
        }
        catch (Exception $e) {
            error_log("SmsService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Basic phone number normalization.
     * Ensures the number is numeric and strips unwanted characters.
     */
    private function formatPhoneNumber(string $phone): string 
    {
        // Remove everything except digits and the plus sign
        $normalized = preg_replace('/[^\d+]/', '', $phone);
        
        // Basic check: minimum length for a valid international number
        return (strlen($normalized) >= 7) ? $normalized : '';
    }
}