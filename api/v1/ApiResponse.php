<?php
/**
 * API Response Helper Class
 * Standardizes all API responses across the system
 * 
 * @package SrijayaERP
 * @version 1.0
 */

class ApiResponse {
    
    /**
     * Send success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $httpCode HTTP status code (default: 200)
     * @param array $meta Additional metadata
     */
    public static function success($data = null, $message = "Success", $httpCode = 200, $meta = []) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => 'v1'
            ], $meta)
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $httpCode HTTP status code (default: 400)
     * @param array $errors Detailed error array
     * @param mixed $debug Debug information (only in development)
     */
    public static function error($message = "An error occurred", $httpCode = 400, $errors = [], $debug = null) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => 'v1'
            ]
        ];
        
        // Add debug info only in development mode
        if ($debug !== null && defined('API_DEBUG') && API_DEBUG === true) {
            $response['debug'] = $debug;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send validation error response
     * 
     * @param array $errors Array of validation errors
     * @param string $message Main error message
     */
    public static function validationError($errors, $message = "Validation failed") {
        self::error($message, 422, $errors);
    }
    
    /**
     * Send unauthorized response
     * 
     * @param string $message Error message
     */
    public static function unauthorized($message = "Unauthorized access") {
        self::error($message, 401);
    }
    
    /**
     * Send forbidden response
     * 
     * @param string $message Error message
     */
    public static function forbidden($message = "Access forbidden") {
        self::error($message, 403);
    }
    
    /**
     * Send not found response
     * 
     * @param string $message Error message
     */
    public static function notFound($message = "Resource not found") {
        self::error($message, 404);
    }
    
    /**
     * Send server error response
     * 
     * @param string $message Error message
     * @param mixed $debug Debug information
     */
    public static function serverError($message = "Internal server error", $debug = null) {
        self::error($message, 500, [], $debug);
    }
    
    /**
     * Send paginated response
     * 
     * @param array $data Response data
     * @param int $total Total records
     * @param int $page Current page
     * @param int $perPage Records per page
     * @param string $message Success message
     */
    public static function paginated($data, $total, $page, $perPage, $message = "Success", $meta = []) {
        $totalPages = ceil($total / $perPage);
        
        self::success($data, $message, 200, array_merge([
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_more' => $page < $totalPages
            ]
        ], $meta));
    }
}
