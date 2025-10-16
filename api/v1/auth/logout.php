<?php
/**
 * Logout API Endpoint
 * Invalidates user session and logs logout action
 * 
 * @route POST /api/v1/auth/logout.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed. Use POST', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Log logout action
$logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'API Logout', 'Mobile POS Logout', CURRENT_DATE, CURRENT_TIME)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'i', $userData['employee_id']);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

// Note: JWT tokens are stateless and cannot be invalidated server-side
// Client should discard the token
// In future, implement token blacklisting in database for enhanced security

ApiResponse::success(null, 'Logout successful');
