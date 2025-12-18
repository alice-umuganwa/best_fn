<?php
/**
 * Database Configuration for XAMPP
 * Disaster Relief Management System
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'disaster_relief');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Disaster Relief Management System');
define('APP_URL', 'http://localhost/BEST/');
define('APP_VERSION', '1.0.0');

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('SESSION_NAME', 'DRMS_SESSION');

// Security Configuration
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 10);

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 5242880); // 5MB in bytes
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Timezone
date_default_timezone_set('America/New_York');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CSRF Token Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600);
