<?php
namespace App\Controllers;

session_start();

use App\Models\Authentication;
use App\Models\NotificationSetting;
use App\Core\Notification;
use App\Core\Security;
use App\Services\EmailService;
use App\Services\SmsService;
use App\Services\SystemNotificationService;
use App\Helpers\SystemHelper;

require_once '../../config/config.php';

class AuthenticationController {
    protected Authentication $authentication;
    protected NotificationSetting $notificationSetting;
    protected Notification $notification;
    protected Security $security;
    protected SystemHelper $systemHelper;

    public function __construct(
        Authentication $authentication,
        NotificationSetting $notificationSetting,
        Notification $notification,
        Security $security,
        SystemHelper $systemHelper
    ) {
        $this->authentication       = $authentication;
        $this->notificationSetting  = $notificationSetting;
        $this->notification         = $notification;
        $this->security             = $security;
        $this->systemHelper         = $systemHelper;
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->systemHelper::sendErrorResponse(
                'Invalid Request',
                'Only POST requests are allowed.'
            );
        }

        $transaction = $_POST['transaction'] ?? null;

        if (!$transaction) {
            $this->systemHelper::sendErrorResponse(
                'Missing Transaction',
                'No transaction type was provided.'
            );
        }

        $transaction = strtolower(trim($transaction));

        match ($transaction) {
            'authenticate'      => $this->authenticate(),
            'otp verification'  => $this->verifyOTP(),
            'resend otp'        => $this->resendOTP(),
            'forgot password'   => $this->forgotPassword(),
            'password reset'    => $this->passwordReset(),
            default             => $this->systemHelper::sendErrorResponse(
                                        'Transaction Failed',
                                        'We encountered an issue while processing your request.'
                                    )
        };
    }

    /* =============================================================================================
        SECTION 1: SAVE METHOD
    ============================================================================================= */

    /* =============================================================================================
        SECTION 2: INSERT METHOD
    ============================================================================================= */

    /* =============================================================================================
        SECTION 3: UPDATE METHOD
    ============================================================================================= */

    /* =============================================================================================
        SECTION 4: FETCH METHOD
    ============================================================================================= */

    /* =============================================================================================
        SECTION 5: DELETE METHOD
    ============================================================================================= */

    /* =============================================================================================
        SECTION 6: CHECK METHOD
    ============================================================================================= */

    /* =============================================================================================
        SECTION 7: GENERATE METHOD
    ============================================================================================= */

    /* =============================================================================================
        SECTION 8: CUSTOM METHOD
    ============================================================================================= */

    public function authenticate() {
        $csrfToken = $_POST['csrf_token'] ?? null;

        if (!$csrfToken || !$this->security::validateCSRFToken($csrfToken, 'login_form')) {
            $this->systemHelper::sendErrorResponse(
                'Invalid Request',
                'Security check failed. Please refresh and try again.'
            );
        }

        $email      = trim($_POST['email'] ?? '');
        $password   = $_POST['password'] ?? '';
        $ipAddress  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $attempts = $this->authentication->checkRateLimited(
            $email, 
            $ipAddress
        );

        if ($attempts >= RATE_LIMITER_THRESHOLD) {
            $this->systemHelper::sendErrorResponse(
                'Too Many Attempts',
                'Please wait a few minutes before trying again.'
            );
        }

        $loginCredentials = $this->authentication->checkLoginCredentialsExist($email);

        if (empty($loginCredentials) || ($loginCredentials['total'] ?? 0) === 0) {
            $this->authentication->insertLoginAttempt(
                null,
                $email,
                $ipAddress,
                false
            );

            $this->systemHelper::sendErrorResponse(
                'Authentication Failed',
                'Invalid credentials. Please check and try again.'
            );
        }

        $credentials           = $this->authentication->fetchLoginCredentials($email);
        $userAccountId         = $credentials['user_account_id'] ?? '';
        $isActive              = $credentials['active'] ?? 'No';
        $userPassword          = $credentials['password'] ?? null;
        $twoFactorAuthEnabled  = $credentials['two_factor_auth'] ?? 'Yes';

        if (!password_verify($password, $userPassword)) {
            $this->authentication->insertLoginAttempt(
                $userAccountId,
                $email,
                $ipAddress,
                false
            );

            $this->systemHelper::sendErrorResponse(
                'Authentication Failed',
                'Invalid credentials. Please check and try again.'
            );
        }

        if ($isActive === 'No') {
            $this->systemHelper::sendErrorResponse(
                'Account Inactive', 
                'Your account is inactive. Please contact your administrator for assistance.'
            );
        }
    
        if ($twoFactorAuthEnabled === 'Yes') {
            $this->handleTwoFactorAuth(
                $userAccountId,
                $email
            );
        }

        $sessionToken   = $this->security::generateToken(6);
        $sessionHash    = $this->security::hashToken($sessionToken);

        $this->authentication->insertLoginAttempt(
            $userAccountId,
            $email,
            $ipAddress,
            true
        );

        $this->authentication->saveSession(
            $userAccountId,
            $sessionHash
        );

        $_SESSION['user_account_id']    = $userAccountId;
        $_SESSION['session_token']      = $sessionToken;

        $this->systemHelper::sendSuccessResponse(
            '',
            '',
            ['redirect_link' => 'apps.php']
        );
    }
    
    private function handleTwoFactorAuth(
        int $userAccountId,
        string $email
    ) {
        $encryptedUserAccountID     = $this->security::encryptData($userAccountId);
        $otp                        = $this->security::generateOtp();
        $otpHash                    = $this->security::hashToken($otp);
        $otpExpiryDate              = date('Y-m-d H:i:s', strtotime('+' . OTP_DURATION . ' minutes'));

        $this->authentication->saveOTP(
            $userAccountId,
            $otpHash,
            $otpExpiryDate
        );

        $placeholder = [
            'OTP_CODE'              => $otp,
            'OTP_CODE_VALIDITY'     => OTP_DURATION . ' minutes'
        ];

        $result = $this->notification->sendNotification(
            1,
            $email,
            [],
            [],
            $placeholder,
            [],
            []
        );

        if ($result) {
            $_SESSION['2fa_user_account_id'] = $userAccountId;

            $this->systemHelper::sendSuccessResponse(
                'OTP Sent',
                'A one-time password has been sent to your registered email address.',
                ['redirect_link' => OTP_VERIFICATION_LINK . $encryptedUserAccountID]
            );
        }
        else {
            $this->systemHelper::sendErrorResponse(
                'Sending OTP Failed',
                is_string($result) ? $result : 'Unable to send OTP. Please try again later.'
            );
        }
    }

    public function verifyOTP() {
        $csrfToken = $_POST['csrf_token'] ?? null;

        if (!$csrfToken || !$this->security::validateCSRFToken($csrfToken, 'otp_form')) {
            $this->systemHelper::sendErrorResponse(
                'Invalid Request',
                'Security check failed. Please refresh and try again.'
            );
        }

        $userAccountId   = $_POST['user_account_id'] ?? '';
        $otpCode1        = $_POST['otp_code_1'] ?? '';
        $otpCode2        = $_POST['otp_code_2'] ?? '';
        $otpCode3        = $_POST['otp_code_3'] ?? '';
        $otpCode4        = $_POST['otp_code_4'] ?? '';
        $otpCode5        = $_POST['otp_code_5'] ?? '';
        $otpCode6        = $_POST['otp_code_6'] ?? '';
        $ipAddress       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $otpVerificationCode = $otpCode1 . $otpCode2 . $otpCode3 . $otpCode4 . $otpCode5 . $otpCode6;

        $checkLoginCredentialsExist     = $this->authentication->checkLoginCredentialsExist($userAccountId);
        $total                          = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $this->systemHelper::sendErrorResponse(
                'Authentication Failed',
                'Invalid credentials. Please check and try again.'
            );
        }

        $credentials    = $this->authentication->fetchLoginCredentials($userAccountId);
        $isActive       = $credentials['active'] ?? 'No';
        $email          = $credentials['email'] ?? 'No';

        $otpDetails         = $this->authentication->fetchOTP($userAccountId);
        $otp                = $otpDetails['otp'] ?? null;
        $otpExpiryDate      = $otpDetails['otp_expiry_date'] ?? '';
        $failedOtpAttempts  = $otpDetails['failed_otp_attempts'] ?? '';        

        if ($isActive === 'No') {
            $this->systemHelper::sendErrorResponse(
                'Account Inactive', 
                'Your account is inactive. Please contact your administrator for assistance.'
            );
        }
        
        if (strtotime(date('Y-m-d H:i:s')) > strtotime($otpExpiryDate)) {
            $this->systemHelper::sendErrorResponse(
                'Expired OTP Code',
                'The OTP code you entered is expired. Please request a new one.'
            );
        }
    
        if (!Security::verifyToken($otpVerificationCode, $otp) || empty($otp)) {
            if ($failedOtpAttempts >= MAX_FAILED_OTP_ATTEMPTS) {
                $this->authentication->updateOTPAsExpired($userAccountId);

                $this->systemHelper::sendErrorResponse(
                    'Invalid OTP Code',
                    'The OTP code you entered is invalid. Please request a new one.'
                );
            }

            $this->authentication->updateFailedOTPAttempts(
                $userAccountId,
                $failedOtpAttempts + 1
            );

            $this->systemHelper::sendErrorResponse(
                'Invalid OTP Code',
                'The OTP code you entered is incorrect.'
            );
        }

        $this->authentication->updateOTPAsExpired($userAccountId);

        $sessionToken   = $this->security::generateToken(6);
        $sessionHash    = $this->security::hashToken($sessionToken);

        $this->authentication->insertLoginAttempt(
            $userAccountId,
            $email,
            $ipAddress,
            true
        );

        $this->authentication->saveSession(
            $userAccountId,
            $sessionHash
        );

        $_SESSION['user_account_id']    = $userAccountId;
        $_SESSION['session_token']      = $sessionToken;

        unset($_SESSION['2fa_user_account_id']);

        $this->systemHelper::sendSuccessResponse(
            '',
            '',
            additionalData: ['redirect_link' => 'apps.php']
        );
    }

    public function forgotPassword() {
        $csrfToken = $_POST['csrf_token'] ?? null;

        if (!$csrfToken || !$this->security::validateCSRFToken($csrfToken, 'forgot_password_form')) {
            $this->systemHelper::sendErrorResponse(
                'Invalid Request',
                'Security check failed. Please refresh and try again.'
            );
        }

        $email = $_POST['email'] ?? '';

        $checkLoginCredentialsExist     = $this->authentication->checkLoginCredentialsExist($email);
        $total                          = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $this->systemHelper::sendErrorResponse(
                'Invalid Credentials',
                'Invalid credentials. Please check and try again.'
            );
        }

        $credentials    = $this->authentication->fetchLoginCredentials($email);
        $userAccountId  = $credentials['user_account_id'] ?? '';
        $isActive       = $credentials['active'] ?? 'No';
        
        $encryptedUserAccountID = $this->security::encryptData($userAccountId);

        if ($isActive === 'No') {
            $this->systemHelper::sendErrorResponse(
                'Account Inactive', 
                'Your account is inactive. Please contact your administrator for assistance.'
            );
        }

        $resetToken             = $this->security::generateToken();
        $resetTokenHash         = $this->security::hashToken($resetToken);
        $resetTokenExpiryDate   = date('Y-m-d H:i:s', strtotime('+' . RESET_PASSWORD_TOKEN_DURATION . ' minutes'));

        $this->authentication->saveResetToken(
            $userAccountId,
            $resetTokenHash,
            $resetTokenExpiryDate
        );

        $placeholder = [
            'RESET_LINK'            => PASSWORD_RECOVERY_LINK  . $encryptedUserAccountID . '&token=' . $resetToken,
            'RESET_LINK_VALIDITY'   => RESET_PASSWORD_TOKEN_DURATION . ' minutes'
        ];

        $result = $this->notification->sendNotification(
            2,
            $email,
            [],
            [],
            $placeholder,
            [],
            []
        );

        if ($result === true) {
            $this->systemHelper::sendSuccessResponse(
                'Password Reset',
                'We have sent a password reset link to your registered email address. Please check your inbox and follow the provided instructions to securely reset your password. If you do not receive the email within a few minutes, please also check your spam folder.',
                ['redirect_link' => 'index.php']
            );
        }
        else {
            $this->systemHelper::sendErrorResponse(
                'Password Reset Failed',
                is_string($result) ? $result : 'Unable to send password reset link. Please try again later.'
            );
        }
    }

    public function passwordReset() {
        $csrfToken = $_POST['csrf_token'] ?? null;

        if (!$csrfToken || !$this->security::validateCSRFToken($csrfToken, 'password_reset_form')) {
            $this->systemHelper::sendErrorResponse(
                'Invalid Request',
                'Security check failed. Please refresh and try again.'
            );
        }

        $userAccountId   = $_POST['user_account_id'] ?? '';
        $newPassword     = password_hash($_POST['new_password'] ?? '', PASSWORD_BCRYPT);

        $checkLoginCredentialsExist = $this->authentication->checkLoginCredentialsExist($userAccountId);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $this->systemHelper::sendErrorResponse(
                'Invalid Credentials',
                'Invalid credentials. Please check and try again.'
            );
        }

        $credentials    = $this->authentication->fetchLoginCredentials($userAccountId);
        $isActive       = $credentials['active'] ?? 'No';

        if ($isActive === 'No') {
            $this->systemHelper::sendErrorResponse(
                'Account Inactive', 
                'Your account is inactive. Please contact your administrator for assistance.'
            );
        }

        $resetTokenDetails = $this->authentication->fetchResetToken($userAccountId);
        $resetTokenExpiryDate = $resetTokenDetails['reset_token_expiry_date'] ?? null;

        if(strtotime(date('Y-m-d H:i:s')) > strtotime($resetTokenExpiryDate)){
            $this->systemHelper::sendErrorResponse(
                'Password Reset Token Expired', 
                'The password reset token has expired. Please request a new link to reset your password.'
            );
        }

        $this->authentication->updateUserPassword(
            $userAccountId,
            $newPassword
        );

        $this->authentication->updateResetTokenAsExpired($userAccountId);

        $this->systemHelper::sendSuccessResponse(
            'Password Reset Success',
            'Your password has been successfully updated. For security reasons, please use your new password to log in.',
            ['redirect_link' => 'index.php']
        );
    }
    
    public function resendOTP() {
        $userAccountId              = $_POST['user_account_id'] ?? '';
        $loginCredentialsDetails    = $this->authentication->fetchLoginCredentials($userAccountId);
        $email                      = $loginCredentialsDetails['email'] ?? null;

        $this->resendOTPCode(
            $userAccountId,
            $email
        );
        
        $response = [
            'success' => true
        ];

        echo json_encode($response);
        exit;
    }

    private function resendOTPCode(
        int $userAccountId, 
        string $email
    ) {
        $otp            = $this->security::generateOtp();
        $otpHash        = $this->security::hashToken($otp);
        $otpExpiryDate  = date('Y-m-d H:i:s', strtotime('+' . OTP_DURATION . ' minutes'));
        
        $this->authentication->saveOTP(
            $userAccountId,
            $otpHash,
            $otpExpiryDate
        );

        $placeholder = [
            'OTP_CODE'              => $otp,
            'OTP_CODE_VALIDITY'     => OTP_DURATION . ' minutes'
        ];

        $result = $this->notification->sendNotification(
            1,
            $email,
            [],
            [],
            $placeholder,
            [],
            []
        );

        if ($result === true) {
            $this->systemHelper::sendSuccessResponse(
                'OTP Resend',
                'A new OTP has been sent to you.'
            );
        }
        else {
            $this->systemHelper::sendErrorResponse(
                'Resending OTP Failed',
                is_string($result) ? $result : 'Unable to resend OTP. Please try again later.'
            );
        }
    }

    /* =============================================================================================
        END OF METHODS
    ============================================================================================= */

    
}

$controller = new AuthenticationController(
    new Authentication(),
    new NotificationSetting(),
    new Notification(new NotificationSetting(), new EmailService(), new SmsService(), new SystemNotificationService()),
    new Security(),
    new SystemHelper()
);

$controller->handleRequest();