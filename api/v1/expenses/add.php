<?php
/**
 * Add Expense API - Create one-time or recurring expense
 * POST /api/v1/expenses/add.php
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
    $requiredFields = ['title', 'amount', 'category_id', 'expense_type'];
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
    
    $expenseType = $input['expense_type']; // 'one-time' or 'recurring'
    $title = trim($input['title']);
    $amount = floatval($input['amount']);
    $categoryId = intval($input['category_id']);
    $userId = $auth['user']['id'];
    
    // Validate amount
    if ($amount <= 0) {
        ApiResponse::error('Amount must be greater than zero', 400);
    }
    
    // Verify category exists
    $catCheckSql = "SELECT category_id FROM expense_categories WHERE category_id = ? AND status = 1";
    $catStmt = $conn->prepare($catCheckSql);
    $catStmt->bind_param("i", $categoryId);
    $catStmt->execute();
    if ($catStmt->get_result()->num_rows === 0) {
        $catStmt->close();
        ApiResponse::error('Invalid category or category is inactive', 400);
    }
    $catStmt->close();
    
    $conn->begin_transaction();
    
    try {
        if ($expenseType === 'recurring') {
            // Handle recurring expense
            $frequency = isset($input['frequency']) ? $input['frequency'] : 'monthly';
            $startDate = isset($input['start_date']) ? $input['start_date'] : date('Y-m-d');
            $endDate = isset($input['end_date']) ? $input['end_date'] : null;
            $paymentMethod = isset($input['payment_method']) ? trim($input['payment_method']) : 'Cash';
            $remindDaysBefore = isset($input['remind_days_before']) ? intval($input['remind_days_before']) : 3;
            
            // Validate frequency
            $validFrequencies = ['daily', 'weekly', 'monthly', 'annually'];
            if (!in_array($frequency, $validFrequencies)) {
                throw new Exception('Invalid frequency. Must be: daily, weekly, monthly, or annually');
            }
            
            // Calculate next due date based on frequency
            $nextDueDate = $startDate;
            
            // Insert recurring expense
            $recurringSql = "INSERT INTO recurring_expenses 
                            (title, amount, category_id, frequency, start_date, next_due_date, 
                             end_date, payment_method, remind_days_before, created_by) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $recurringStmt = $conn->prepare($recurringSql);
            $recurringStmt->bind_param(
                "sdisssssii",
                $title, $amount, $categoryId, $frequency, $startDate, 
                $nextDueDate, $endDate, $paymentMethod, $remindDaysBefore, $userId
            );
            
            if (!$recurringStmt->execute()) {
                throw new Exception("Failed to create recurring expense: " . $recurringStmt->error);
            }
            
            $recurringId = $recurringStmt->insert_id;
            $recurringStmt->close();
            
            $conn->commit();
            
            ApiResponse::success(
                [
                    'recurring_id' => $recurringId,
                    'type' => 'recurring',
                    'title' => $title,
                    'amount' => $amount,
                    'frequency' => $frequency,
                    'next_due_date' => $nextDueDate
                ],
                "Recurring expense created successfully",
                201
            );
            
        } else {
            // Handle one-time expense
            $expenseDate = isset($input['expense_date']) ? $input['expense_date'] : date('Y-m-d H:i:s');
            $paymentMethod = isset($input['payment_method']) ? trim($input['payment_method']) : 'Cash';
            $status = isset($input['status']) ? $input['status'] : 'paid';
            $referenceNo = isset($input['reference_no']) ? trim($input['reference_no']) : null;
            $notes = isset($input['notes']) ? trim($input['notes']) : null;
            $attachmentUrl = isset($input['attachment_url']) ? trim($input['attachment_url']) : null;
            
            // Validate status
            $validStatuses = ['paid', 'pending', 'overdue'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Invalid status. Must be: paid, pending, or overdue');
            }
            
            // Insert expense
            $expenseSql = "INSERT INTO expenses 
                          (title, amount, category_id, expense_date, payment_method, 
                           status, reference_no, notes, attachment_url, created_by) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $expenseStmt = $conn->prepare($expenseSql);
            $expenseStmt->bind_param(
                "sdissssssi",
                $title, $amount, $categoryId, $expenseDate, $paymentMethod,
                $status, $referenceNo, $notes, $attachmentUrl, $userId
            );
            
            if (!$expenseStmt->execute()) {
                throw new Exception("Failed to create expense: " . $expenseStmt->error);
            }
            
            $expenseId = $expenseStmt->insert_id;
            $expenseStmt->close();
            
            $conn->commit();
            
            ApiResponse::success(
                [
                    'expense_id' => $expenseId,
                    'type' => 'one-time',
                    'title' => $title,
                    'amount' => $amount,
                    'expense_date' => $expenseDate,
                    'status' => $status
                ],
                "Expense created successfully",
                201
            );
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Add Expense API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to create expense",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
