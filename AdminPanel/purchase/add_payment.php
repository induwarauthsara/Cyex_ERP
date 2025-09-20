<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Validate required fields
    $required_fields = ['po_id', 'amount', 'method'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    $po_id = (int)$_POST['po_id'];
    $amount = (float)$_POST['amount'];
    $method = $_POST['method'];
    $reference = isset($_POST['reference']) ? $_POST['reference'] : null;
    $note = isset($_POST['note']) ? $_POST['note'] : null;

    // Validate payment method
    $valid_methods = ['cash', 'bank_transfer', 'cheque', 'credit_card'];
    if (!in_array($method, $valid_methods)) {
        throw new Exception('Invalid payment method');
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Get PO details and validate amount
    $po_sql = "
        SELECT 
            po.total_amount,
            COALESCE(SUM(sp.amount), 0) as paid_amount,
            po.supplier_id,
            s.supplier_name,
            po.po_number
        FROM purchase_orders po
        LEFT JOIN supplier_payments sp ON po.po_id = sp.po_id
        LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
        WHERE po.po_id = ?
        GROUP BY po.po_id
    ";

    $po_stmt = mysqli_prepare($con, $po_sql);
    mysqli_stmt_bind_param($po_stmt, 'i', $po_id);
    mysqli_stmt_execute($po_stmt);
    $po_result = mysqli_stmt_get_result($po_stmt);
    $po_data = mysqli_fetch_assoc($po_result);

    if (!$po_data) {
        throw new Exception('Purchase order not found');
    }

    $balance = $po_data['total_amount'] - $po_data['paid_amount'];

    if ($amount <= 0) {
        throw new Exception('Payment amount must be greater than zero');
    }

    if ($amount > $balance) {
        throw new Exception('Payment amount cannot exceed remaining balance');
    }

    // Insert payment record
    $payment_sql = "
        INSERT INTO supplier_payments (
            supplier_id, po_id, date, amount, method, 
            reference_no, note, created_at, created_by
        ) VALUES (?, ?, CURDATE(), ?, ?, ?, ?, NOW(), ?)
    ";

    $payment_stmt = mysqli_prepare($con, $payment_sql);
    mysqli_stmt_bind_param(
        $payment_stmt,
        'iidsss',
        $po_data['supplier_id'],
        $po_id,
        $amount,
        $method,
        $reference,
        $note,
        $_SESSION['employee_id']
    );

    if (!$payment_stmt->execute()) {
        throw new Exception('Failed to add payment record');
    }

    // Update supplier credit balance if necessary
    $new_balance = $balance - $amount;
    if ($new_balance <= 0) {
        $update_supplier_sql = "
            UPDATE suppliers 
            SET credit_balance = credit_balance - ? 
            WHERE supplier_id = ?
        ";

        if (!insert_query(
            $update_supplier_sql,
            "Updated supplier credit balance for supplier #{$po_data['supplier_id']}",
            "Update Supplier Balance"
        )) {
            throw new Exception('Failed to update supplier credit balance');
        }
    }

    // Log transaction
    transaction_log(
        'SUPPLIER_PAYMENT',
        "Payment for PO #{$po_data['po_number']} to {$po_data['supplier_name']}",
        $amount
    );

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Payment added successfully',
        'remaining_balance' => $new_balance
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();