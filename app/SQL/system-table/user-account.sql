/* =============================================================================================
  TABLE: USER ACCOUNT
============================================================================================= */

DROP TABLE IF EXISTS user_account;

CREATE TABLE user_account (
  user_account_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
  file_as VARCHAR(300) NOT NULL,
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  profile_picture VARCHAR(500),
  active ENUM('Yes', 'No') DEFAULT 'No',
  two_factor_auth ENUM('Yes', 'No') DEFAULT 'No',
  multiple_session ENUM('Yes', 'No') DEFAULT 'No',
  last_connection_date DATETIME,
  last_failed_connection_date DATETIME,
  last_password_change DATETIME,
  last_password_reset_request DATETIME,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,     
  last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_log_by INT UNSIGNED DEFAULT 1,
  FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

/* =============================================================================================
  INDEX: USER ACCOUNT
============================================================================================= */

CREATE INDEX idx_user_account_email ON user_account(email);
CREATE INDEX idx_user_account_phone ON user_account(phone);

/* =============================================================================================
  INITIAL VALUES: USER ACCOUNT
============================================================================================= */

INSERT INTO user_account (file_as, email, password, phone, active, two_factor_auth)
VALUES 
('Bot', 'bot@christianmotors.ph', '$2y$10$Qu3TEV2u0SBF1jdb2DzB6.OcMChTDStXHEOdX47Y01sOGkl4UnOaK', '123-456-7890', 'Yes', 'No'),
('Lawrence Agulto', 'l.agulto@christianmotors.ph', '$2y$10$Qu3TEV2u0SBF1jdb2DzB6.OcMChTDStXHEOdX47Y01sOGkl4UnOaK', '123-456-7890', 'Yes', 'No');

/* =============================================================================================
  END OF TABLE DEFINITIONS
============================================================================================= */