<?php
/**
 * Pay Recurring Expense API
 * POST /api/v1/expenses/pay_recurring.php
 * 
 * Marks a recurring expense as paid for the current cycle
 * - Creates an entry in the expenses table
 * - Updates the next_due_date in recurring_expenses
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

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['recurring_id'])) {
        ApiResponse::error('Missing recurring_id', 400);
    }
    
    $recurringId = intval($input['recurring_id']);
    $paymentDate = isset($input['payment_date']) ? $input['payment_date'] : date('Y-m-d H:i:s');
    $paymentMethod = isset($input['payment_method']) ? trim($input['payment_method']) : null;
    $notes = isset($input['notes']) ? trim($input['notes']) : null;
    $attachmentUrl = isset($input['attachment_url']) ? trim($input['attachment_url']) : null;
    $userId = $auth['user']['id'];
    
    // Fetch recurring expense details
    $fetchSql = "SELECT * FROM recurring_expenses WHERE recurring_id = ? AND is_active = 1";
    $fetchStmt = $conn->prepare($fetchSql);
    $fetchStmt->bind_param("i", $recurringId);
    $fetchStmt->execute();
    $recurringExpense = $fetchStmt->get_result()->fetch_assoc();
    $fetchStmt->close();
    
    if (!$recurringExpense) {
        ApiResponse::error('Recurring expense not found or inactive', 404);
    }
    
    // Use recurring expense's payment method if not provided
    if (!$paymentMethod) {
        $paymentMethod = $recurringExpense['payment_method'];
    }
    
    $conn->begin_transaction();
    
    try {
        // 1. Create expense record
        $expenseSql = "INSERT INTO expenses 
                      (title, amount, category_id, expense_date, payment_method, 
                       status, recurring_ref_id, notes, attachment_url, created_by) 
                      VALUES (?, ?, ?, ?, ?, 'paid', ?, ?, ?, ?)";
        
        $expenseStmt = $conn->prepare($expenseSql);
        $expenseStmt->bind_param(
            "sdississi",
            $recurringExpense['title'],
            $recurringExpense['amount'],
            $recurringExpense['category_id'],
            $paymentDate,
            $paymentMethod,
            $recurringId,
            $notes,
            $attachmentUrl,
            $userId
        );
        
        if (!$expenseStmt->execute()) {
            throw new Exception("Failed to create expense record: " . $expenseStmt->error);
        }
        
        $expenseId = $expenseStmt->insert_id;
        $expenseStmt->close();
        
        // 2. Calculate next due date
        $currentDueDate = $recurringExpense['next_due_date'];
        $frequency = $recurringExpense['frequency'];
        
        $nextDueDate = date('Y-m-d', strtotime($currentDueDate . ' + 1 ' . $frequency));
        
        // Handle different frequencies
        switch ($frequency) {
            case 'daily':
                $nextDueDate = date('Y-m-d', strtotime($currentDueDate . ' +1 day'));
                break;
            case 'weekly':
                $nextDueDate = date('Y-m-d', strtotime($currentDueDate . ' +1 week'));
                break;
            case 'monthly':
                $nextDueDate = date('Y-m-d', strtotime($currentDueDate . ' +1 month'));
                break;
            case 'annually':
                $nextDueDate = date('Y-m-d', strtotime($currentDueDate . ' +1 year'));
                break;
        }
        
        // Check if next due date exceeds end date (if set)
        $shouldDeactivate = false;
        if ($recurringExpense['end_date'] && $nextDueDate > $recurringExpense['end_date']) {
            $shouldDeactivate = true;
        }
        
        // 3. Update recurring expense
        if ($shouldDeactivate) {
            $updateSql = "UPDATE recurring_expenses 
                         SET next_due_date = ?, is_active = 0, updated_at = CURRENT_TIMESTAMP 
                         WHERE recurring_id = ?";
        } else {
            $updateSql = "UPDATE recurring_expenses 
                         SET next_due_date = ?, updated_at = CURRENT_TIMESTAMP 
                         WHERE recurring_id = ?";
        }
        
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $nextDueDate, $recurringId);
        
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update recurring expense: " . $updateStmt->error);
        }
        
        $updateStmt->close();
        
        $conn->commit();
        
        ApiResponse::success(
            [
                'expense_id' => $expenseId,
                'recurring_id' => $recurringId,
                'amount' => (float)$recurringExpense['amount'],
                'payment_date' => $paymentDate,
                'next_due_date' => $shouldDeactivate ? null : $nextDueDate,
                'is_active' => !$shouldDeactivate,
                'deactivated' => $shouldDeactivate,
                'deactivation_reason' => $shouldDeactivate ? 'End date reached' : null
            ],
            $shouldDeactivate 
                ? "Payment recorded and recurring expense completed" 
                : "Payment recorded successfully. Next payment due: " . $nextDueDate,
            201
        );
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Pay Recurring API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to process payment",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
