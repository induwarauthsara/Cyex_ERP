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
    if (!isset($_POST['po_id'])) {
        throw new Exception('Purchase order ID is required');
    }

    $po_id = (int)$_POST['po_id'];

    // Start transaction
    mysqli_begin_transaction($con);

    // Check if PO exists and is in draft status
    $check_sql = "SELECT status, total_amount FROM purchase_orders WHERE po_id = ? AND status = 'draft'";
    $check_stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 'i', $po_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) === 0) {
        throw new Exception('Purchase order not found or cannot be deleted');
    }

    $po_data = mysqli_fetch_assoc($result);

    // Delete purchase order items
    $delete_items_sql = "DELETE FROM purchase_order_items WHERE po_id = ?";
    if (!insert_query($delete_items_sql, "Deleted PO items for PO #$po_id", "Delete PO Items")) {
        throw new Exception('Failed to delete purchase order items');
    }

    // Delete the purchase order
    $delete_po_sql = "DELETE FROM purchase_orders WHERE po_id = ?";
    if (!insert_query($delete_po_sql, "Deleted PO #$po_id", "Delete PO")) {
        throw new Exception('Failed to delete purchase order');
    }

    // Log the transaction
    transaction_log(
        'DELETE_PO',
        "Deleted Purchase Order #$po_id",
        $po_data['total_amount']
    );

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Purchase order deleted successfully'
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