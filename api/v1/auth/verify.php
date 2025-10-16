<?php
/**
 * Verify Token API Endpoint
 * Checks if provided token is valid
 * 
 * @route GET /api/v1/auth/verify.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed. Use GET', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Return user data if token is valid
ApiResponse::success([
    'user' => [
        'id' => $userData['employee_id'],
        'name' => $userData['employee_name'],
        'role' => $userData['employee_role']
    ],
    'token_valid' => true,
    'expires_at' => date('Y-m-d H:i:s', $userData['exp'])
], 'Token is valid');
