<?php
require_once('../../../inc/config.php');

header('Content-Type: application/json');

if (!isset($_POST['po_id'])) {
    echo json_encode(['success' => false, 'message' => 'Purchase order ID is required']);
    exit;
}

$po_id = intval($_POST['po_id']);

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Check if PO exists and is in draft status
    $check_query = "SELECT status, po_number, total_amount FROM purchase_orders WHERE po_id = ?";
    $stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $po_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $po = mysqli_fetch_assoc($result);

    if (!$po) {
        throw new Exception('Purchase order not found');
    }

    if ($po['status'] !== 'draft') {
        throw new Exception('Only draft purchase orders can be deleted');
    }

    // Delete PO items first due to foreign key constraint
    $delete_items_query = "DELETE FROM purchase_order_items WHERE po_id = ?";
    $stmt = mysqli_prepare($con, $delete_items_query);
    mysqli_stmt_bind_param($stmt, "i", $po_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error deleting purchase order items');
    }

    // Delete the PO
    $delete_po_query = "DELETE FROM purchase_orders WHERE po_id = ?";
    $stmt = mysqli_prepare($con, $delete_po_query);
    mysqli_stmt_bind_param($stmt, "i", $po_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error deleting purchase order');
    }

    // Log the action
    $msg = "Deleted purchase order #{$po['po_number']} (Draft)";
    $action = "Delete PO";
    insert_query("SELECT 1", $msg, $action); // Using dummy query since insert_query requires one

    // Log the transaction
    transaction_log(
        'PO Deleted',
        "Deleted purchase order #{$po['po_number']}",
        $po['total_amount']
    );

    // Commit transaction
    mysqli_commit($con);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
