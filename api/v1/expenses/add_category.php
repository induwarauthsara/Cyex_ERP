<?php
/**
 * Add Expense Category API
 * POST /api/v1/expenses/add_category.php
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $requiredFields = ['category_name'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        ApiResponse::error(
            'Missing required fields',
            400,
            ['missing_fields' => $missingFields]
        );
    }
    
    $categoryName = trim($input['category_name']);
    $description = isset($input['description']) ? trim($input['description']) : null;
    $colorCode = isset($input['color_code']) ? trim($input['color_code']) : '#808080';
    $icon = isset($input['icon']) ? trim($input['icon']) : 'money-bill';
    $status = isset($input['status']) ? intval($input['status']) : 1;
    
    // Validate color code format
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $colorCode)) {
        ApiResponse::error('Invalid color code format. Use hex format like #FF5733', 400);
    }
    
    // Check if category name already exists
    $checkSql = "SELECT category_id FROM expense_categories WHERE category_name = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $categoryName);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        ApiResponse::error('Category name already exists', 409);
    }
    $checkStmt->close();
    
    // Insert new category
    $sql = "INSERT INTO expense_categories 
            (category_name, description, color_code, icon, status) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $categoryName, $description, $colorCode, $icon, $status);
    
    if ($stmt->execute()) {
        $categoryId = $stmt->insert_id;
        $stmt->close();
        
        ApiResponse::success(
            [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'description' => $description,
                'color_code' => $colorCode,
                'icon' => $icon,
                'status' => $status
            ],
            "Category created successfully",
            201
        );
    } else {
        throw new Exception("Failed to insert category: " . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Add Category API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to create category",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
