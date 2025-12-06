<?php
/**
 * Get Category Details API
 * GET /api/v1/expenses/category_details.php?category_id=X
 * 
 * Returns details statistics and recent transactions for a specific category
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
        require_once __DIR__ . '/../../../inc/config.php';
        if (isset($con)) {
            $conn = $con;
        }
    }
}

try {
    if (!isset($_GET['category_id'])) {
        ApiResponse::error('Category ID is required', 400);
    }
    
    $categoryId = intval($_GET['category_id']);
    
    // 1. Get Category Info
    $catSql = "SELECT * FROM expense_categories WHERE category_id = ?";
    $catStmt = $conn->prepare($catSql);
    $catStmt->bind_param("i", $categoryId);
    $catStmt->execute();
    $category = $catStmt->get_result()->fetch_assoc();
    $catStmt->close();
    
    if (!$category) {
        ApiResponse::error('Category not found', 404);
    }
    
    // 2. Get Statistics
    // Total All Time
    $statsSql = "SELECT 
                    COUNT(*) as total_count,
                    COALESCE(SUM(amount), 0) as total_amount,
                    COALESCE(SUM(CASE WHEN DATE_FORMAT(expense_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') THEN amount ELSE 0 END), 0) as current_month_amount,
                    MAX(expense_date) as last_expense_date
                 FROM expenses 
                 WHERE category_id = ?";
                 
    $statsStmt = $conn->prepare($statsSql);
    $statsStmt->bind_param("i", $categoryId);
    $statsStmt->execute();
    $stats = $statsStmt->get_result()->fetch_assoc();
    $statsStmt->close();
    
    // 3. Get Recent Transactions
    $transSql = "SELECT 
                    expense_id, 
                    title, 
                    amount, 
                    expense_date, 
                    status,
                    payment_method
                 FROM expenses 
                 WHERE category_id = ? 
                 ORDER BY expense_date DESC 
                 LIMIT 10";
                 
    $transStmt = $conn->prepare($transSql);
    $transStmt->bind_param("i", $categoryId);
    $transStmt->execute();
    $transResult = $transStmt->get_result();
    
    $transactions = [];
    while ($row = $transResult->fetch_assoc()) {
        $transactions[] = [
            'expense_id' => (int)$row['expense_id'],
            'title' => $row['title'],
            'amount' => (float)$row['amount'],
            'date' => $row['expense_date'],
            'status' => $row['status'],
            'method' => $row['payment_method']
        ];
    }
    $transStmt->close();
    
    $response = [
        'category' => [
            'id' => (int)$category['category_id'],
            'name' => $category['category_name'],
            'description' => $category['description'],
            'color' => $category['color_code'],
            'icon' => $category['icon'],
            'status' => (int)$category['status']
        ],
        'stats' => [
            'total_count' => (int)$stats['total_count'],
            'total_amount' => (float)$stats['total_amount'],
            'current_month_amount' => (float)$stats['current_month_amount'],
            'last_used' => $stats['last_expense_date']
        ],
        'recent_transactions' => $transactions
    ];
    
    ApiResponse::success($response, "Category details retrieved successfully");
    
} catch (Exception $e) {
    error_log("Category Details API Error: " . $e->getMessage());
    ApiResponse::error("Failed to retrieve category details", 500);
}
