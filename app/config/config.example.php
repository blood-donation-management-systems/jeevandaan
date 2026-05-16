<?php
/**
 * JeevanDaan Configuration
 */

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'YOUR_DB_PASSWORD');
define('DB_NAME', 'jeevandaan');

// App Settings
define('APP_NAME', 'JeevanDaan');
define('APP_URL', 'http://localhost/jeevandaan/public');
define('APP_ROOT', dirname(dirname(__DIR__)));

// Google OAuth (Replace with your credentials)
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', APP_URL . '/auth/google-callback');

// Upload Settings - NOW IN PUBLIC FOLDER
define('UPLOAD_PATH', APP_ROOT . '/public/uploads/');
define('UPLOAD_URL', APP_URL . '/uploads/');  // New: URL for browser access
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);

// Donation Rules
define('MIN_DONATION_AGE', 18);
define('MAX_DONATION_AGE', 65);
define('MIN_WEIGHT_KG', 45);
define('DONATION_INTERVAL_DAYS', 90);

// Timezone
date_default_timezone_set('Asia/Kathmandu');

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'YOUR_EMAIL@gmail.com');  // ← Replace with your Gmail
define('SMTP_PASSWORD', 'YOUR_APP_PASSWORD');     // ← Replace with App Password (no spaces)
define('SMTP_FROM_EMAIL', 'YOUR_EMAIL@gmail.com');
define('SMTP_FROM_NAME', 'JeevanDaan');
