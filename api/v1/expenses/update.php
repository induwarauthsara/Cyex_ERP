<?php
/**
 * Update Expense API
 * PUT /api/v1/expenses/update.php
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Method not allowed', 405);
}

$auth = ApiAuth::authenticate();
if (!$auth['success']) {
    ApiResponse::error($auth['message'], 401);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['expense_id'])) {
        ApiResponse::error('Missing expense_id', 400);
    }
    
    $expenseId = intval($input['expense_id']);
    
    // Check if expense exists
    $checkSql = "SELECT expense_id FROM expenses WHERE expense_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $expenseId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows === 0) {
        $checkStmt->close();
        ApiResponse::error('Expense not found', 404);
    }
    $checkStmt->close();
    
    // Build update query dynamically
    $updates = [];
    $params = [];
    $types = '';
    
    if (isset($input['title'])) {
        $updates[] = "title = ?";
        $params[] = trim($input['title']);
        $types .= 's';
    }
    
    if (isset($input['amount'])) {
        $amount = floatval($input['amount']);
        if ($amount <= 0) {
            ApiResponse::error('Amount must be greater than zero', 400);
        }
        $updates[] = "amount = ?";
        $params[] = $amount;
        $types .= 'd';
    }
    
    if (isset($input['category_id'])) {
        $updates[] = "category_id = ?";
        $params[] = intval($input['category_id']);
        $types .= 'i';
    }
    
    if (isset($input['expense_date'])) {
        $updates[] = "expense_date = ?";
        $params[] = $input['expense_date'];
        $types .= 's';
    }
    
    if (isset($input['payment_method'])) {
        $updates[] = "payment_method = ?";
        $params[] = trim($input['payment_method']);
        $types .= 's';
    }
    
    if (isset($input['status'])) {
        $validStatuses = ['paid', 'pending', 'overdue'];
        if (!in_array($input['status'], $validStatuses)) {
            ApiResponse::error('Invalid status', 400);
        }
        $updates[] = "status = ?";
        $params[] = $input['status'];
        $types .= 's';
    }
    
    if (isset($input['reference_no'])) {
        $updates[] = "reference_no = ?";
        $params[] = trim($input['reference_no']);
        $types .= 's';
    }
    
    if (isset($input['notes'])) {
        $updates[] = "notes = ?";
        $params[] = trim($input['notes']);
        $types .= 's';
    }
    
    if (isset($input['attachment_url'])) {
        $updates[] = "attachment_url = ?";
        $params[] = trim($input['attachment_url']);
        $types .= 's';
    }
    
    if (empty($updates)) {
        ApiResponse::error('No fields to update', 400);
    }
    
    $sql = "UPDATE expenses SET " . implode(", ", $updates) . ", updated_at = CURRENT_TIMESTAMP WHERE expense_id = ?";
    $params[] = $expenseId;
    $types .= 'i';
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        $stmt->close();
        ApiResponse::success(
            ['expense_id' => $expenseId],
            "Expense updated successfully"
        );
    } else {
        throw new Exception("Failed to update expense: " . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Update Expense API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to update expense",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
