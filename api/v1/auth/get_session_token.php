<?php
/**
 * Get Session Token API
 * 
 * Get JWT token from existing PHP session for backward compatibility
 * GET /api/v1/auth/get_session_token.php
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../../../inc/config.php');

// Check if user is logged in via PHP session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['employee_id'])) {
    ApiResponse::error('Not authenticated', 401);
}

try {
    // Get employee details
    $employee_id = $_SESSION['employee_id'];
    
    $sql = "SELECT employ_id, emp_name, role FROM employees WHERE employ_id = ? AND status = '1'";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $employee_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $employee = mysqli_fetch_assoc($result);
    
    if (!$employee) {
        ApiResponse::error('Employee not found', 404);
    }
    
    // Generate JWT token
    $payload = [
        'employee_id' => (int)$employee['employ_id'],
        'employee_name' => $employee['emp_name'],
        'employee_role' => $employee['role'],
        'iat' => time(),
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    
    $token = generateJWT($payload);
    
    ApiResponse::success([
        'token' => $token,
        'employee' => [
            'id' => (int)$employee['employ_id'],
            'name' => $employee['emp_name'],
            'role' => $employee['role']
        ]
    ]);
    
} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}

/**
 * Generate JWT token
 */
function generateJWT($payload) {
    $secret = JWT_SECRET;
    
    // Create header
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    
    // Encode header and payload
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    
    // Create signature
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    // Create JWT
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}
