<?php
/**
 * API Authentication Helper Class
 * Handles JWT token generation, validation, and session management
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/ApiResponse.php';

class ApiAuth {
    
    // JWT Secret Key - Should be moved to environment variable in production
    private static $jwtSecret = 'srijaya_erp_secret_key_2025_change_in_production';
    private static $tokenExpiry = 86400; // 24 hours in seconds
    
    /**
     * Generate JWT token
     * 
     * @param int $employeeId Employee ID
     * @param string $employeeName Employee name
     * @param string $employeeRole Employee role
     * @return string JWT token
     */
    public static function generateToken($employeeId, $employeeName, $employeeRole) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $payload = json_encode([
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'employee_role' => $employeeRole,
            'iat' => time(),
            'exp' => time() + self::$tokenExpiry
        ]);
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$jwtSecret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    /**
     * Validate and decode JWT token
     * 
     * @param string $token JWT token
     * @return array|false Token payload or false if invalid
     */
    public static function validateToken($token) {
        if (empty($token)) {
            return false;
        }
        
        $tokenParts = explode('.', $token);
        if (count($tokenParts) !== 3) {
            return false;
        }
        
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenParts;
        
        // Verify signature
        $signature = self::base64UrlDecode($base64UrlSignature);
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$jwtSecret, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
        
        // Decode payload
        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);
        
        // Check expiration
        if (isset($payload['exp']) && time() > $payload['exp']) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Get token from request headers
     * 
     * @return string|null Token or null if not found
     */
    public static function getTokenFromRequest() {
        $headers = getallheaders();
        
        // Check Authorization header
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        
        // Check X-Auth-Token header
        if (isset($headers['X-Auth-Token'])) {
            return $headers['X-Auth-Token'];
        }
        
        // Check query parameter (not recommended for production)
        if (isset($_GET['token'])) {
            return $_GET['token'];
        }
        
        return null;
    }
    
    /**
     * Require authentication for API endpoint
     * Validates token and returns user data or exits with error
     * 
     * @return array User data from token
     */
    public static function requireAuth() {
        $token = self::getTokenFromRequest();
        
        if (!$token) {
            ApiResponse::unauthorized("Authentication token required");
        }
        
        $userData = self::validateToken($token);
        
        if (!$userData) {
            ApiResponse::unauthorized("Invalid or expired token");
        }
        
        // Verify user still exists and is active
        global $con;
        $employeeId = intval($userData['employee_id']);
        $query = "SELECT employ_id, emp_name, role, status FROM employees WHERE employ_id = ? AND status = '1'";
        $stmt = mysqli_prepare($con, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $employeeId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) === 0) {
                ApiResponse::unauthorized("User account is inactive or not found");
            }
            
            mysqli_stmt_close($stmt);
        }
        
        return $userData;
    }
    
    /**
     * Require specific role for API endpoint
     * 
     * @param array $allowedRoles Array of allowed roles
     * @param array $userData User data from requireAuth()
     */
    public static function requireRole($allowedRoles, $userData) {
        if (!in_array($userData['employee_role'], $allowedRoles)) {
            ApiResponse::forbidden("Insufficient permissions for this action");
        }
    }
    
    /**
     * Base64 URL encode
     * 
     * @param string $data Data to encode
     * @return string Encoded string
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     * 
     * @param string $data Data to decode
     * @return string Decoded string
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * Create session from token (for backward compatibility)
     * 
     * @param array $userData User data from token
     */
    public static function createSession($userData) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['employee_id'] = $userData['employee_id'];
        $_SESSION['employee_name'] = $userData['employee_name'];
        $_SESSION['employee_role'] = $userData['employee_role'];
    }
}
