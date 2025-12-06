<?php
/**
 * List Expenses API - Get expenses with filtering
 * GET /api/v1/expenses/list.php
 * 
 * Query Parameters:
 * - start_date: Filter from this date (Y-m-d format)
 * - end_date: Filter to this date (Y-m-d format)
 * - category_id: Filter by category
 * - status: Filter by status (paid, pending, overdue)
 * - payment_method: Filter by payment method
 * - limit: Records per page (default: 50)
 * - offset: Pagination offset (default: 0)
 * - search: Search in title or reference_no
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

// Check if required columns exist (database migration check)
$checkColumns = $conn->query("SHOW COLUMNS FROM expenses LIKE 'amount_paid'");
if ($checkColumns->num_rows === 0) {
    ApiResponse::error(
        "Database migration required. Please run expenses_migration.sql file. The 'amount_paid' column is missing from the expenses table.",
        500,
        [],
        "Database schema is outdated. Migration file: expenses_migration.sql"
    );
}

try {
    // Get query parameters
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $paymentMethod = isset($_GET['payment_method']) ? $_GET['payment_method'] : null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    
    // Validate limit
    if ($limit > 500) {
        $limit = 500; // Maximum limit
    }
    
    // Build query
    $sql = "SELECT 
                e.expense_id,
                e.reference_no,
                e.title,
                e.amount,
                e.amount_paid,
                e.category_id,
                ec.category_name,
                ec.color_code,
                ec.icon,
                e.expense_date,
                e.payment_method,
                e.status,
                e.recurring_ref_id,
                e.attachment_url,
                e.notes,
                e.created_by,
                e.created_at,
                COALESCE(emp.emp_name, 'Unknown') as created_by_name,
                (e.amount - e.amount_paid) as remaining_amount,
                CASE 
                    WHEN e.amount > 0 THEN ROUND((e.amount_paid / e.amount) * 100, 2)
                    ELSE 0 
                END as payment_percentage
            FROM expenses e
            INNER JOIN expense_categories ec ON e.category_id = ec.category_id
            LEFT JOIN employees emp ON e.created_by = emp.employ_id";
    
    $conditions = [];
    $params = [];
    $types = '';
    
    // Apply filters
    if ($startDate) {
        $conditions[] = "DATE(e.expense_date) >= ?";
        $params[] = $startDate;
        $types .= 's';
    }
    
    if ($endDate) {
        $conditions[] = "DATE(e.expense_date) <= ?";
        $params[] = $endDate;
        $types .= 's';
    }
    
    if ($categoryId) {
        $conditions[] = "e.category_id = ?";
        $params[] = $categoryId;
        $types .= 'i';
    }
    
    if ($status) {
        $conditions[] = "e.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if ($paymentMethod) {
        $conditions[] = "e.payment_method = ?";
        $params[] = $paymentMethod;
        $types .= 's';
    }
    
    if ($search) {
        $conditions[] = "(e.title LIKE ? OR e.reference_no LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM (" . $sql . ") as count_query";
    $countStmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $totalCount = $countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();
    
    // Add sorting and pagination
    $sql .= " ORDER BY e.expense_date DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = [
            'expense_id' => (int)$row['expense_id'],
            'reference_no' => $row['reference_no'],
            'title' => $row['title'],
            'amount' => (float)$row['amount'],
            'amount_paid' => (float)$row['amount_paid'],
            'remaining_amount' => (float)$row['remaining_amount'],
            'payment_percentage' => (float)$row['payment_percentage'],
            'category' => [
                'category_id' => (int)$row['category_id'],
                'category_name' => $row['category_name'],
                'color_code' => $row['color_code'],
                'icon' => $row['icon']
            ],
            'expense_date' => $row['expense_date'],
            'payment_method' => $row['payment_method'],
            'status' => $row['status'],
            'is_recurring' => $row['recurring_ref_id'] !== null,
            'recurring_ref_id' => $row['recurring_ref_id'] ? (int)$row['recurring_ref_id'] : null,
            'attachment_url' => $row['attachment_url'],
            'notes' => $row['notes'],
            'created_by' => [
                'id' => (int)$row['created_by'],
                'name' => $row['created_by_name']
            ],
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    
    ApiResponse::success(
        $expenses,
        "Expenses retrieved successfully",
        200,
        [
            'total_count' => (int)$totalCount,
            'returned_count' => count($expenses),
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $totalCount
        ]
    );
    
} catch (Exception $e) {
    error_log("List Expenses API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to retrieve expenses",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
