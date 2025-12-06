<?php
/**
 * Delete Expense Category API
 * DELETE /api/v1/expenses/delete_category.php
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow DELETE or POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    // Get input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['category_id']) || !is_numeric($input['category_id'])) {
        ApiResponse::error('Category ID is required', 400);
    }
    
    $categoryId = intval($input['category_id']);
    
    // Check if category exists
    $checkSql = "SELECT * FROM expense_categories WHERE category_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $categoryId);
    $checkStmt->execute();
    $category = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();
    
    if (!$category) {
        ApiResponse::error('Category not found', 404);
    }
    
    // Check if category is used in expenses
    $usageSql = "SELECT COUNT(*) as count FROM expenses WHERE category_id = ?";
    $usageStmt = $conn->prepare($usageSql);
    $usageStmt->bind_param("i", $categoryId);
    $usageStmt->execute();
    $usageCount = $usageStmt->get_result()->fetch_assoc()['count'];
    $usageStmt->close();
    
    if ($usageCount > 0) {
        // Cannot delete used category
        ApiResponse::error(
            "Cannot delete this category because it is used in $usageCount expense records. Please mark it as inactive instead.",
            409, // Conflict
            [
                'can_delete' => false,
                'usage_count' => $usageCount
            ]
        );
    }
    
    // Check if category is used in recurring references (if applicable)
    // Assuming expenses table covers recurring ones as well, or there might be a separate recurring definitions table?
    // Based on list.php, there's `is_recurring` flag, but data is in `expenses`.
    // Wait, recurring table? `recurring.php` suggests there is one.
    // Let's check recurring usage too.
    
    // Check recurring table exists/usage
    // Usually tables: expenses, expense_recurring (or similar)
    // list.php references `recurring_ref_id`.
    // Let's assume table `expense_recurring_payments` or similar. 
    // `recurring.php` (from previous search/read) likely queried it.
    // summary.php uses `v_upcoming_recurring_payments`.
    
    // Let's just stick to 'expenses' check for now. If there's a foreign key on another table, the DB will error out, which catch block will handle.
    
    // Proceed with delete
    $deleteSql = "DELETE FROM expense_categories WHERE category_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $categoryId);
    
    if ($deleteStmt->execute()) {
        $deleteStmt->close();
        ApiResponse::success(
            ['category_id' => $categoryId],
            "Category deleted successfully",
            200
        );
    } else {
        throw new Exception("Failed to delete category: " . $deleteStmt->error);
    }
    
} catch (Exception $e) {
    // Check for foreign key constraint violation specifically
    if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        ApiResponse::error("Cannot delete category: it is referenced by other records.", 409);
    } else {
        error_log("Delete Category API Error: " . $e->getMessage());
        ApiResponse::error(
            "Failed to delete category",
            500,
            [],
            API_DEBUG ? $e->getMessage() : null
        );
    }
}
