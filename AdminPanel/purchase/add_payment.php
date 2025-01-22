<?php
require_once('../../../inc/config.php');

header('Content-Type: application/json');

if (!isset($_POST['po_id'], $_POST['payment_amount'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Get PO details
    $po_query = "SELECT po.*, s.supplier_name 
                 FROM purchase_orders po
                 JOIN suppliers s ON po.supplier_id = s.supplier_id
                 WHERE po_id = ?";
    $stmt = mysqli_prepare($con, $po_query);
    mysqli_stmt_bind_param($stmt, "i", $_POST['po_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $po = mysqli_fetch_assoc($result);

    if (!$po) {
        throw new Exception('Purchase order not found');
    }

    // Calculate current balance
    $paid_query = "SELECT COALESCE(SUM(amount), 0) as paid 
                   FROM supplier_payments 
                   WHERE supplier_id = ? AND reference_no = ?";
    $stmt = mysqli_prepare($con, $paid_query);
    mysqli_stmt_bind_param($stmt, "is", $po['supplier_id'], $po['po_number']);
    mysqli_stmt_execute($stmt);
    $paid_result = mysqli_stmt_get_result($stmt);
    $paid = mysqli_fetch_assoc($paid_result)['paid'];

    $balance = $po['total_amount'] - $paid;
    $payment_amount = floatval($_POST['payment_amount']);

    if ($payment_amount <= 0) {
        throw new Exception('Invalid payment amount');
    }

    if ($payment_amount > $balance) {
        throw new Exception('Payment amount exceeds remaining balance');
    }

    // Insert payment
    $query = "INSERT INTO supplier_payments (
                supplier_id, 
                date, 
                amount, 
                method, 
                reference_no, 
                note,
                created_by
              ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "isdssis",
        $po['supplier_id'],
        $_POST['payment_date'],
        $payment_amount,
        $_POST['payment_method'],
        $po['po_number'],
        $_POST['payment_note'],
        $_SESSION['employee_id']
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error recording payment');
    }

    // Log the action
    $msg = "Payment of {$payment_amount} added for PO #{$po['po_number']} to {$po['supplier_name']}";
    $action = "Add Payment";
    insert_query("SELECT 1", $msg, $action); // Using dummy query since insert_query requires one

    // Log the transaction
    transaction_log(
        'Supplier Payment',
        "Payment to {$po['supplier_name']} for PO #{$po['po_number']}",
        $payment_amount
    );

    // Update supplier credit balance if needed
    $balance_query = "UPDATE suppliers 
                     SET credit_balance = credit_balance - ? 
                     WHERE supplier_id = ?";
    $stmt = mysqli_prepare($con, $balance_query);
    mysqli_stmt_bind_param($stmt, "di", $payment_amount, $po['supplier_id']);
    mysqli_stmt_execute($stmt);

    // Commit transaction
    mysqli_commit($con);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
