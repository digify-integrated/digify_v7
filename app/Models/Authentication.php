<?php
namespace App\Models;

use App\Core\Model;

class Authentication extends Model {

    /* =============================================================================================
        SECTION 1: SAVE METHODS
    ============================================================================================= */

    public function saveResetToken(
        int $p_user_account_id,
        string $p_reset_token,
        string $p_reset_token_expiry_date
    ) {
        $sql = 'CALL saveResetToken(
            :p_user_account_id,
            :p_reset_token,
            :p_reset_token_expiry_date
        )';
        
        return $this->query($sql, [
            'p_user_account_id'             => $p_user_account_id,
            'p_reset_token'                 => $p_reset_token,
            'p_reset_token_expiry_date'     => $p_reset_token_expiry_date
        ]);
    }
    
    public function saveSession(
        int $p_user_account_id,
        string $p_session_token
    ) {
        $sql = 'CALL saveSession(
            :p_user_account_id,
            :p_session_token
        )';
        
        return $this->query($sql, [
            'p_user_account_id'     => $p_user_account_id,
            'p_session_token'       => $p_session_token
        ]);
    }
    
    public function saveOTP(
        int $p_user_account_id,
        string $p_otp,
        string $otp_expiry_date
    ) {
        $sql = 'CALL saveOTP(
            :p_user_account_id,
            :p_otp,
            :otp_expiry_date
        )';

        return $this->query($sql, [
            'p_user_account_id'     => $p_user_account_id,
            'p_otp'                 => $p_otp,
            'otp_expiry_date'       => $otp_expiry_date
        ]);
    }

    /* =============================================================================================
        SECTION 2: INSERT METHODS
    ============================================================================================= */

    public function insertLoginAttempt(
        null|int $p_user_account_id,
        string $p_email,
        string $p_ip_address,
        int $p_success
    )    {
        $sql = 'CALL insertLoginAttempt(
            :p_user_account_id,
            :p_email,
            :p_ip_address,
            :p_success
        )';

        return $this->query($sql, [
            'p_user_account_id'     => $p_user_account_id,
            'p_email'               => $p_email,
            'p_ip_address'          => $p_ip_address,
            'p_success'             => $p_success
        ]);
    }

    /* =============================================================================================
        SECTION 3: UPDATE METHODS
    =============================================================================================  */

    public function updateFailedOTPAttempts(
        int $p_user_account_id,
        int $p_failed_otp_attempts
    ) {
        $sql = 'CALL updateFailedOTPAttempts(
            :p_user_account_id,
            :p_failed_otp_attempts
        )';
        
        return $this->query($sql, [
            'p_user_account_id'         => $p_user_account_id,
            'p_failed_otp_attempts'     => $p_failed_otp_attempts
        ]);
    }

    public function updateUserPassword(
        int $p_user_account_id,
        string $p_password
    ) {
        $sql = 'CALL updateUserPassword(
            :p_user_account_id,
            :p_password
        )';
        
        return $this->query($sql, [
            'p_user_account_id'     => $p_user_account_id,
            'p_password'            => $p_password
        ]);
    }

    public function updateOTPAsExpired(
        int $p_user_account_id
    ) {
        $sql = 'CALL updateOTPAsExpired(
            :p_user_account_id
        )';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    public function updateResetTokenAsExpired(
        int $p_user_account_id
    ) {
        $sql = 'CALL updateResetTokenAsExpired(
            :p_user_account_id
        )';
        
        return $this->query($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    /* =============================================================================================
        SECTION 4: FETCH METHODS
    ============================================================================================= */

    public function fetchLoginCredentials(
        string $p_credential
    ) {
        $sql = 'CALL fetchLoginCredentials(
            :p_credential
        )';
        
        return $this->fetch($sql, [
            'p_credential' => $p_credential
        ]);
    }

    public function fetchOTP(
        int $p_user_account_id
    ) {
        $sql = 'CALL fetchOTP(
            :p_user_account_id
        )';
        
        return $this->fetch($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    public function fetchResetToken(
        int $p_user_account_id
    ) {
        $sql = 'CALL fetchResetToken(
            :p_user_account_id
        )';
        
        return $this->fetch($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    public function fetchSession(
        int $p_user_account_id
    ) {
        $sql = 'CALL fetchSession(
            :p_user_account_id
        )';

        return $this->fetch($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    public function fetchAppModuleStack(
        int $p_user_account_id
    ) {
        $sql = 'CALL fetchAppModuleStack(
            :p_user_account_id
        )';
        
        return $this->fetchAll($sql, [
            'p_user_account_id' => $p_user_account_id
        ]);
    }

    /* =============================================================================================
        SECTION 5: DELETE METHODS
    ============================================================================================= */

    /* =============================================================================================
        SECTION 6: CHECK METHODS
    ============================================================================================= */

    public function checkLoginCredentialsExist(
        string $p_email
    ) {
        $sql = 'CALL checkLoginCredentialsExist(
            :p_email
        )';
        
        return $this->fetch($sql, [
            'p_email' => $p_email
        ]);
    }

    public function checkUserSystemActionPermission(
        int $p_user_account_id,
        int $p_system_action_id
    ) {
        $sql = 'CALL checkUserSystemActionPermission(
            :p_user_account_id,
            :p_system_action_id
        )';
        
        return $this->fetch($sql, [
            'p_user_account_id'     => $p_user_account_id,
            'p_system_action_id'    => $p_system_action_id
        ]);
    }

    public function checkUserPermission(
        int $p_user_account_id,
        int $p_menu_item_id,
        string $p_access_type
    ) {
        $sql = 'CALL checkUserPermission(
            :p_user_account_id,
            :p_menu_item_id,
            :p_access_type
        )';
        
        return $this->fetch($sql, [
            'p_user_account_id'     => $p_user_account_id,
            'p_menu_item_id'        => $p_menu_item_id,
            'p_access_type'         => $p_access_type
        ]);
    }
    
    public function checkRateLimited(
        string $p_email,
        string $p_ip_address
    )    {
        $window = RATE_LIMITER_WINDOW;

        $sql = 'CALL checkRateLimited(
            :p_email,
            :p_ip_address,
            :p_window
        )';

        $result = $this->fetch($sql, [
            'p_email'       => $p_email,
            'p_ip_address'  => $p_ip_address,
            'p_window'      => $window
        ]);

        return (int) ($result['total'] ?? 0);
    }

    /* =============================================================================================
        SECTION 7: GENERATE METHODS
    ============================================================================================= */

    /* =============================================================================================
        END OF METHODS
    ============================================================================================= */
}