<?php
/**
 * List Recurring Expenses API
 * GET /api/v1/expenses/recurring.php
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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

$auth = ApiAuth::authenticate();
if (!$auth['success']) {
    ApiResponse::error($auth['message'], 401);
}

try {
    $isActive = isset($_GET['is_active']) ? intval($_GET['is_active']) : 1;
    
    $sql = "SELECT 
                re.*,
                ec.category_name,
                ec.color_code,
                ec.icon,
                DATEDIFF(re.next_due_date, CURRENT_DATE()) as days_until_due
            FROM recurring_expenses re
            INNER JOIN expense_categories ec ON re.category_id = ec.category_id
            WHERE re.is_active = ?
            ORDER BY re.next_due_date ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $isActive);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recurring = [];
    while ($row = $result->fetch_assoc()) {
        $recurring[] = [
            'recurring_id' => (int)$row['recurring_id'],
            'title' => $row['title'],
            'amount' => (float)$row['amount'],
            'category' => [
                'category_id' => (int)$row['category_id'],
                'category_name' => $row['category_name'],
                'color_code' => $row['color_code'],
                'icon' => $row['icon']
            ],
            'frequency' => $row['frequency'],
            'start_date' => $row['start_date'],
            'next_due_date' => $row['next_due_date'],
            'end_date' => $row['end_date'],
            'payment_method' => $row['payment_method'],
            'remind_days_before' => (int)$row['remind_days_before'],
            'days_until_due' => (int)$row['days_until_due'],
            'is_active' => (int)$row['is_active'],
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    
    ApiResponse::success(
        $recurring,
        "Recurring expenses retrieved successfully",
        200,
        ['count' => count($recurring)]
    );
    
} catch (Exception $e) {
    error_log("Recurring Expenses API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to retrieve recurring expenses",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
