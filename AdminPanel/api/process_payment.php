<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

// Only Admin can access this API
session_start();
$employee_role = $_SESSION['employee_role'] ?? '';
$employee_id = $_SESSION['employee_id'] ?? 0;

if ($employee_role !== "Admin") {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once(__DIR__ . '/../../inc/config.php');

// Validate required POST data
$required_fields = ['grn_id', 'payment_amount', 'payment_method'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

$grn_id = (int)$_POST['grn_id'];
$payment_amount = (float)$_POST['payment_amount'];
$payment_method = mysqli_real_escape_string($con, $_POST['payment_method']);
$reference_no = mysqli_real_escape_string($con, $_POST['reference_no'] ?? '');
$payment_note = mysqli_real_escape_string($con, $_POST['payment_note'] ?? '');

// Validate payment amount
if ($payment_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Payment amount must be greater than 0']);
    exit;
}

// Start transaction
mysqli_autocommit($con, FALSE);

try {
    // Get GRN details
    $grn_query = "
        SELECT 
            grn.grn_id,
            grn.grn_number,
            grn.supplier_id,
            grn.total_amount,
            grn.paid_amount,
            grn.outstanding_amount,
            grn.payment_status,
            s.supplier_name,
            s.credit_balance
        FROM goods_receipt_notes grn
        LEFT JOIN suppliers s ON grn.supplier_id = s.supplier_id
        WHERE grn.grn_id = $grn_id
    ";
    
    $grn_result = mysqli_query($con, $grn_query);
    
    if (!$grn_result || mysqli_num_rows($grn_result) == 0) {
        throw new Exception('GRN not found');
    }
    
    $grn_data = mysqli_fetch_assoc($grn_result);
    
    // Validate payment amount doesn't exceed outstanding amount
    if ($payment_amount > $grn_data['outstanding_amount']) {
        throw new Exception('Payment amount cannot exceed outstanding amount of LKR ' . number_format($grn_data['outstanding_amount'], 2));
    }
    
    // Calculate new amounts
    $new_paid_amount = $grn_data['paid_amount'] + $payment_amount;
    $new_outstanding_amount = $grn_data['total_amount'] - $new_paid_amount;
    
    // Determine new payment status
    $new_payment_status = 'unpaid';
    if ($new_outstanding_amount <= 0.01) { // Account for floating point precision
        $new_payment_status = 'paid';
        $new_outstanding_amount = 0; // Ensure it's exactly 0
        $new_paid_amount = $grn_data['total_amount']; // Ensure it matches total
    } elseif ($new_paid_amount > 0) {
        $new_payment_status = 'partial';
    }
    
    // 1. Insert into supplier_payments table
    $payment_insert_query = "
        INSERT INTO supplier_payments (
            supplier_id, 
            date, 
            amount, 
            method, 
            reference_no, 
            note, 
            created_by, 
            grn_id
        ) VALUES (
            {$grn_data['supplier_id']}, 
            CURDATE(), 
            $payment_amount, 
            '$payment_method', 
            '$reference_no', 
            '$payment_note', 
            $employee_id, 
            $grn_id
        )
    ";
    
    if (!mysqli_query($con, $payment_insert_query)) {
        throw new Exception('Failed to insert payment record: ' . mysqli_error($con));
    }
    
    $payment_id = mysqli_insert_id($con);
    
    // 2. Update goods_receipt_notes table
    $grn_update_query = "
        UPDATE goods_receipt_notes 
        SET 
            paid_amount = $new_paid_amount,
            outstanding_amount = $new_outstanding_amount,
            payment_status = '$new_payment_status',
            payment_method = '$payment_method',
            payment_reference = '$reference_no',
            payment_notes = '$payment_note',
            updated_at = NOW()
        WHERE grn_id = $grn_id
    ";
    
    if (!mysqli_query($con, $grn_update_query)) {
        throw new Exception('Failed to update GRN record: ' . mysqli_error($con));
    }
    
    // 3. Update supplier credit_balance
    $new_supplier_balance = $grn_data['credit_balance'] - $payment_amount;
    $supplier_update_query = "
        UPDATE suppliers 
        SET 
            credit_balance = $new_supplier_balance,
            updated_at = NOW()
        WHERE supplier_id = {$grn_data['supplier_id']}
    ";
    
    if (!mysqli_query($con, $supplier_update_query)) {
        throw new Exception('Failed to update supplier balance: ' . mysqli_error($con));
    }
    
    // 4. Insert into transaction_log
    $transaction_description = "GRN Payment: {$grn_data['grn_number']} - Payment to {$grn_data['supplier_name']} via $payment_method";
    if ($reference_no) {
        $transaction_description .= " (Ref: $reference_no)";
    }
    
    $transaction_log_query = "
        INSERT INTO transaction_log (
            transaction_type,
            description,
            amount,
            transaction_date,
            transaction_time,
            employ_id
        ) VALUES (
            'GRN Payment',
            '$transaction_description',
            $payment_amount,
            CURDATE(),
            CURTIME(),
            $employee_id
        )
    ";
    
    if (!mysqli_query($con, $transaction_log_query)) {
        throw new Exception('Failed to log transaction: ' . mysqli_error($con));
    }
    
    // 5. Insert into action_log
    $action_description = "Processed payment of LKR " . number_format($payment_amount, 2) . " for GRN {$grn_data['grn_number']} to supplier {$grn_data['supplier_name']}";
    
    $action_log_query = "
        INSERT INTO action_log (
            employee_id,
            action,
            description,
            date,
            time
        ) VALUES (
            $employee_id,
            'GRN Payment Processed',
            '$action_description',
            CURDATE(),
            CURTIME()
        )
    ";
    
    if (!mysqli_query($con, $action_log_query)) {
        throw new Exception('Failed to log action: ' . mysqli_error($con));
    }
    
    // Commit transaction
    mysqli_commit($con);
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'data' => [
            'payment_id' => $payment_id,
            'grn_number' => $grn_data['grn_number'],
            'supplier_name' => $grn_data['supplier_name'],
            'payment_amount' => number_format($payment_amount, 2),
            'new_paid_amount' => number_format($new_paid_amount, 2),
            'new_outstanding_amount' => number_format($new_outstanding_amount, 2),
            'payment_status' => $new_payment_status,
            'payment_method' => $payment_method,
            'reference_no' => $reference_no
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($con);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    // Restore autocommit
    mysqli_autocommit($con, TRUE);
    
    if (isset($con)) {
        mysqli_close($con);
    }
}
?>