<?php
/**
 * Expense Categories API - List all categories
 * GET /api/v1/expenses/categories.php
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

// Verify authentication
$auth = ApiAuth::authenticate();
if (!$auth['success']) {
    ApiResponse::error($auth['message'], 401);
}

// Ensure database connection is available
if (!isset($conn)) {
    if (isset($con)) {
        $conn = $con;
    } else {
        // Attempt to load main config if not available
        require_once __DIR__ . '/../../../inc/config.php';
        if (isset($con)) {
            $conn = $con;
        }
    }
}

if (!isset($conn)) {
    ApiResponse::serverError("Database connection failed - conn not set");
}

try {
    // Get query parameters
    $status = isset($_GET['status']) ? intval($_GET['status']) : null;
    $includeInactive = isset($_GET['include_inactive']) && $_GET['include_inactive'] === 'true';
    
    // Build query
    $sql = "SELECT 
                category_id,
                category_name,
                description,
                color_code,
                icon,
                status,
                created_at
            FROM expense_categories";
    
    $conditions = [];
    $params = [];
    
    // Filter by status if specified
    if ($status !== null) {
        $conditions[] = "status = ?";
        $params[] = $status;
    } elseif (!$includeInactive) {
        // Default: only show active categories
        $conditions[] = "status = 1";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY category_name ASC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('i', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'category_id' => (int)$row['category_id'],
            'category_name' => $row['category_name'],
            'description' => $row['description'],
            'color_code' => $row['color_code'],
            'icon' => $row['icon'],
            'status' => (int)$row['status'],
            'status_label' => $row['status'] == 1 ? 'Active' : 'Inactive',
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    
    ApiResponse::success(
        $categories,
        "Categories retrieved successfully",
        200,
        ['total_count' => count($categories)]
    );
    
} catch (Exception $e) {
    error_log("Categories API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to retrieve categories",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
