<?php
require_once dirname(__DIR__) . '/vendor/autoload.php'; 

# -------------------------------------------------------------
# Timezone Configuration
# -------------------------------------------------------------

date_default_timezone_set('Asia/Manila');

# -------------------------------------------------------------

# -------------------------------------------------------------
# App Configuration
# -------------------------------------------------------------

define('APP_NAME', 'Digify');                           // The name of your app

# -------------------------------------------------------------

# -------------------------------------------------------------
# Database Configuration
# -------------------------------------------------------------

define('DB_HOST', 'localhost');                         // Database host
define('DB_NAME', 'digifydb');                          // Database name
define('DB_USER', 'root');                              // Database username
define('DB_PASS', '');                                  // Database password
define('DB_CHARSET', 'utf8mb4');    

# -------------------------------------------------------------

# -------------------------------------------------------------
# Encryption Configuration
# -------------------------------------------------------------

define('ENCRYPTION_KEY', '4b$Gy#89%q*aX@^p&cT!sPv6(5w)zSd+R');

# -------------------------------------------------------------

# -------------------------------------------------------------
# File Upload Configuration
# -------------------------------------------------------------

define('UPLOAD_DIR', __DIR__ . '/../uploads');          // Directory to store uploaded files
define('MAX_FILE_SIZE', 10485760);                      // Maximum file upload size (10MB)

# -------------------------------------------------------------

# -------------------------------------------------------------
# Mail Configuration
# -------------------------------------------------------------

define('MAIL_SMTP_SERVER', 'smtp.hostinger.com');                   // SMTP server
define('MAIL_SMTP_PORT', 465);                                      // SMTP port (usually 587 for TLS)
define('MAIL_USERNAME', 'cgmi-noreply@christianmotors.ph');         // SMTP username
define('MAIL_PASSWORD', 'P@ssw0rd');                                // SMTP password
define('MAIL_FROM_EMAIL', 'cgmi-noreply@christianmotors.ph');       // Email "from" address
define('MAIL_FROM_NAME', 'CGMI No Reply');                          // Name to show in "from" field
define('MAIL_SMTP_SECURE', 'ssl');                                  // Mail SMTP Secure
define('MAIL_SMTP_AUTH', true);                                     // Mail SMTP Authentication

# -------------------------------------------------------------

# -------------------------------------------------------------
# Default User Interface Images
# -------------------------------------------------------------

define('DEFAULT_AVATAR_IMAGE', './assets/images/default/default-avatar.jpg');
define('DEFAULT_BG_IMAGE', './assets/images/default/default-bg.jpg');
define('DEFAULT_LOGIN_LOGO_IMAGE', './assets/images/default/default-logo-placeholder.png');
define('DEFAULT_MENU_LOGO_IMAGE', './assets/images/default/default-menu-logo.png');
define('DEFAULT_MODULE_ICON_IMAGE', './assets/images/default/default-module-icon.svg');
define('DEFAULT_FAVICON_IMAGE', './assets/images/default/default-favicon.svg');
define('DEFAULT_COMPANY_LOGO', './assets/images/default/default-company-logo.png');
define('DEFAULT_APP_MODULE_LOGO', './assets/images/default/app-module-logo.png');
define('DEFAULT_PLACEHOLDER_IMAGE', './assets/images/default/default-image-placeholder.jpeg');
define('DEFAULT_ID_PLACEHOLDER_FRONT', './assets/images/default/id-placeholder-front.jpg');
define('DEFAULT_UPLOAD_PLACEHOLDER', './assets/images/default/upload-placeholder.png');
define('DEFAULT_PRODUCT_IMAGE', './assets/images/default/default-product.png');

# -------------------------------------------------------------

# -------------------------------------------------------------
# Security Configuration
# -------------------------------------------------------------

define('MAX_FAILED_LOGIN_ATTEMPTS', 5);
define('MAX_FAILED_OTP_ATTEMPTS', 5);
define('PASSWORD_RECOVERY_LINK', 'http://localhost/digify_v3/password-reset.php?id=');
define('OTP_VERIFICATION_LINK', 'otp-verification.php?id=');
define('RESET_PASSWORD_TOKEN_DURATION', 10);
define('OTP_DURATION', 5);
define('RATE_LIMITER_THRESHOLD', 5);
define('RATE_LIMITER_WINDOW', 900);

# -------------------------------------------------------------