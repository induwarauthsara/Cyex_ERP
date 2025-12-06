<?php
/**
 * Update Expense Category API
 * POST /api/v1/expenses/update_category.php
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST or PUT requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
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
    if (!isset($input['category_id']) || !is_numeric($input['category_id'])) {
        ApiResponse::error('Category ID is required', 400);
    }
    
    $categoryId = intval($input['category_id']);
    
    // Fetch existing category to ensure it exists
    $checkSql = "SELECT * FROM expense_categories WHERE category_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $categoryId);
    $checkStmt->execute();
    $existingCategory = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();
    
    if (!$existingCategory) {
        ApiResponse::error('Category not found', 404);
    }
    
    // Prepare update data
    $categoryName = isset($input['category_name']) ? trim($input['category_name']) : $existingCategory['category_name'];
    $description = isset($input['description']) ? trim($input['description']) : $existingCategory['description'];
    $colorCode = isset($input['color_code']) ? trim($input['color_code']) : $existingCategory['color_code'];
    $icon = isset($input['icon']) ? trim($input['icon']) : $existingCategory['icon'];
    $status = isset($input['status']) ? intval($input['status']) : $existingCategory['status'];
    
    // Validate color code format if changed
    if (isset($input['color_code']) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $colorCode)) {
        ApiResponse::error('Invalid color code format. Use hex format like #FF5733', 400);
    }
    
    // Check if new category name already exists for another category
    if (isset($input['category_name']) && $categoryName !== $existingCategory['category_name']) {
        $nameCheckSql = "SELECT category_id FROM expense_categories WHERE category_name = ? AND category_id != ?";
        $nameCheckStmt = $conn->prepare($nameCheckSql);
        $nameCheckStmt->bind_param("si", $categoryName, $categoryId);
        $nameCheckStmt->execute();
        if ($nameCheckStmt->get_result()->num_rows > 0) {
            $nameCheckStmt->close();
            ApiResponse::error('Category name already exists', 409);
        }
        $nameCheckStmt->close();
    }
    
    // Update category
    $sql = "UPDATE expense_categories 
            SET category_name = ?, description = ?, color_code = ?, icon = ?, status = ? 
            WHERE category_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $categoryName, $description, $colorCode, $icon, $status, $categoryId);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Return updated category
        ApiResponse::success(
            [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'description' => $description,
                'color_code' => $colorCode,
                'icon' => $icon,
                'status' => $status
            ],
            "Category updated successfully",
            200
        );
    } else {
        throw new Exception("Failed to update category: " . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Update Category API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to update category",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
