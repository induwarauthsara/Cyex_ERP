<?php
/**
 * Set Session API
 * 
 * Set PHP session from JWT token for backward compatibility
 * POST /api/v1/auth/set_session.php
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed. Use POST', 405);
}

// Verify JWT token
$userData = ApiAuth::requireAuth();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session variables from token data
$_SESSION['employee_id'] = $userData['employee_id'];
$_SESSION['employee_name'] = $userData['employee_name'];
$_SESSION['employee_role'] = $userData['employee_role'];

ApiResponse::success([
    'session_set' => true,
    'employee_id' => $_SESSION['employee_id']
], 'Session initialized successfully');
