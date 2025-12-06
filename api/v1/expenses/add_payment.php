<?php
/**
 * Add Partial Payment API
 * POST /api/v1/expenses/add_payment.php
 * 
 * Records a partial or full payment for an expense
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

$auth = ApiAuth::authenticate();
if (!$auth['success']) {
    ApiResponse::error($auth['message'], 401);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $requiredFields = ['expense_id', 'payment_amount'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
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
    
    $expenseId = intval($input['expense_id']);
    $paymentAmount = floatval($input['payment_amount']);
    $paymentDate = isset($input['payment_date']) ? $input['payment_date'] : date('Y-m-d H:i:s');
    $paymentMethod = isset($input['payment_method']) ? trim($input['payment_method']) : 'Cash';
    $referenceNo = isset($input['reference_no']) ? trim($input['reference_no']) : null;
    $notes = isset($input['notes']) ? trim($input['notes']) : null;
    $userId = $auth['user']['id'];
    
    // Validate payment amount
    if ($paymentAmount <= 0) {
        ApiResponse::error('Payment amount must be greater than zero', 400);
    }
    
    // Get expense details
    $expenseSql = "SELECT expense_id, title, amount, amount_paid, status 
                   FROM expenses WHERE expense_id = ?";
    $expenseStmt = $conn->prepare($expenseSql);
    $expenseStmt->bind_param("i", $expenseId);
    $expenseStmt->execute();
    $expense = $expenseStmt->get_result()->fetch_assoc();
    $expenseStmt->close();
    
    if (!$expense) {
        ApiResponse::error('Expense not found', 404);
    }
    
    // Check if payment exceeds remaining amount
    $remainingAmount = $expense['amount'] - $expense['amount_paid'];
    if ($paymentAmount > $remainingAmount) {
        ApiResponse::error(
            'Payment amount exceeds remaining balance',
            400,
            [
                'remaining_amount' => (float)$remainingAmount,
                'attempted_payment' => $paymentAmount
            ]
        );
    }
    
    $conn->begin_transaction();
    
    try {
        // Insert payment record
        $paymentSql = "INSERT INTO expense_payments 
                      (expense_id, payment_amount, payment_date, payment_method, 
                       reference_no, notes, created_by) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $paymentStmt = $conn->prepare($paymentSql);
        $paymentStmt->bind_param(
            "idssssi",
            $expenseId, $paymentAmount, $paymentDate, $paymentMethod,
            $referenceNo, $notes, $userId
        );
        
        if (!$paymentStmt->execute()) {
            throw new Exception("Failed to record payment: " . $paymentStmt->error);
        }
        
        $paymentId = $paymentStmt->insert_id;
        $paymentStmt->close();
        
        // Note: Trigger will auto-update amount_paid and status
        // But we'll also do it manually for consistency
        $newAmountPaid = $expense['amount_paid'] + $paymentAmount;
        
        // Determine new status
        $newStatus = 'unpaid';
        if ($newAmountPaid >= $expense['amount']) {
            $newStatus = 'paid';
        } elseif ($newAmountPaid > 0) {
            $newStatus = 'partial';
        }
        
        // Update expense
        $updateSql = "UPDATE expenses 
                     SET amount_paid = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
                     WHERE expense_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("dsi", $newAmountPaid, $newStatus, $expenseId);
        
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update expense: " . $updateStmt->error);
        }
        
        $updateStmt->close();
        
        $conn->commit();
        
        // Calculate remaining
        $newRemainingAmount = $expense['amount'] - $newAmountPaid;
        
        ApiResponse::success(
            [
                'payment_id' => $paymentId,
                'expense_id' => $expenseId,
                'expense_title' => $expense['title'],
                'payment_amount' => $paymentAmount,
                'total_amount' => (float)$expense['amount'],
                'amount_paid' => $newAmountPaid,
                'remaining_amount' => $newRemainingAmount,
                'payment_percentage' => round(($newAmountPaid / $expense['amount']) * 100, 2),
                'status' => $newStatus,
                'is_fully_paid' => $newStatus === 'paid'
            ],
            "Payment recorded successfully",
            201
        );
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Add Payment API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to record payment",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
