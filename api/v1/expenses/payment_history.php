<?php
/**
 * Get Expense Payment History API
 * GET /api/v1/expenses/payment_history.php?expense_id=X
 * 
 * Retrieves all payment transactions for a specific expense
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

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
    $expenseId = isset($_GET['expense_id']) ? intval($_GET['expense_id']) : null;
    
    if (!$expenseId) {
        ApiResponse::error('Missing expense_id parameter', 400);
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
    
    // Get payment history
    $paymentSql = "SELECT 
                    ep.payment_id,
                    ep.payment_amount,
                    ep.payment_date,
                    ep.payment_method,
                    ep.reference_no,
                    ep.notes,
                    ep.created_by,
                    COALESCE(emp.emp_name, 'Unknown') as created_by_name,
                    ep.created_at
                FROM expense_payments ep
                LEFT JOIN employees emp ON ep.created_by = emp.employ_id
                WHERE ep.expense_id = ?
                ORDER BY ep.payment_date DESC, ep.created_at DESC";
    
    $paymentStmt = $conn->prepare($paymentSql);
    $paymentStmt->bind_param("i", $expenseId);
    $paymentStmt->execute();
    $result = $paymentStmt->get_result();
    
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = [
            'payment_id' => (int)$row['payment_id'],
            'payment_amount' => (float)$row['payment_amount'],
            'payment_date' => $row['payment_date'],
            'payment_method' => $row['payment_method'],
            'reference_no' => $row['reference_no'],
            'notes' => $row['notes'],
            'created_by' => [
                'id' => (int)$row['created_by'],
                'name' => $row['created_by_name']
            ],
            'created_at' => $row['created_at']
        ];
    }
    
    $paymentStmt->close();
    
    // Calculate summary
    $totalAmount = (float)$expense['amount'];
    $amountPaid = (float)$expense['amount_paid'];
    $remainingAmount = $totalAmount - $amountPaid;
    $paymentPercentage = $totalAmount > 0 ? round(($amountPaid / $totalAmount) * 100, 2) : 0;
    
    ApiResponse::success(
        [
            'expense' => [
                'expense_id' => (int)$expense['expense_id'],
                'title' => $expense['title'],
                'total_amount' => $totalAmount,
                'amount_paid' => $amountPaid,
                'remaining_amount' => $remainingAmount,
                'payment_percentage' => $paymentPercentage,
                'status' => $expense['status']
            ],
            'payments' => $payments,
            'summary' => [
                'total_payments' => count($payments),
                'total_paid' => $amountPaid,
                'remaining' => $remainingAmount,
                'is_fully_paid' => $expense['status'] === 'paid'
            ]
        ],
        "Payment history retrieved successfully"
    );
    
} catch (Exception $e) {
    error_log("Payment History API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to retrieve payment history",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
