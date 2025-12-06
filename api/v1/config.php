<?php
/**
 * API Configuration File
 * Central configuration for all API endpoints
 * 
 * @package SrijayaERP
 * @version 1.0
 */

// API Debug Mode - Set to false in production
define('API_DEBUG', true);

// API Version
define('API_VERSION', 'v1');

// CORS Settings
define('API_CORS_ENABLED', true);
define('API_CORS_ORIGINS', '*'); // Change to specific domain in production

// Rate Limiting
define('API_RATE_LIMIT_ENABLED', false); // Enable in production
define('API_RATE_LIMIT_REQUESTS', 100); // Requests per minute
define('API_RATE_LIMIT_PERIOD', 60); // Period in seconds

// JWT Settings
define('JWT_SECRET', 'srijaya_erp_secret_key_2025_change_in_production'); // CHANGE IN PRODUCTION!
define('JWT_EXPIRY', 86400); // 24 hours

// Include main config
require_once __DIR__ . '/../../inc/config.php';

// Alias connection variable for compatibility
if (isset($con) && !isset($conn)) {
    $conn = $con;
}

// Handle CORS
if (API_CORS_ENABLED) {
    header('Access-Control-Allow-Origin: ' . API_CORS_ORIGINS);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-Token');
    header('Access-Control-Max-Age: 3600');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Set default content type
header('Content-Type: application/json');

// Error handling for API
function apiErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    
    if (API_DEBUG) {
        error_log(json_encode($error));
    }
    
    return true;
}

set_error_handler('apiErrorHandler');

// Exception handler for API
function apiExceptionHandler($exception) {
    require_once __DIR__ . '/ApiResponse.php';
    
    $message = API_DEBUG ? $exception->getMessage() : 'An unexpected error occurred';
    $debug = API_DEBUG ? [
        'exception' => get_class($exception),
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ] : null;
    
    ApiResponse::serverError($message, $debug);
}

set_exception_handler('apiExceptionHandler');
