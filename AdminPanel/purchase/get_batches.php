<?php
require_once('../../../inc/config.php');

header('Content-Type: application/json');

if (!isset($_GET['po_item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Purchase order item ID is required']);
    exit;
}

try {
    // Get product ID from PO item
    $query = "SELECT product_id FROM purchase_order_items WHERE po_item_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $_GET['po_item_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);

    if (!$item) {
        throw new Exception('Item not found');
    }

    // Get active batches for the product
    $query = "SELECT batch_id, batch_number, quantity, expiry_date, cost, selling_price
              FROM product_batch 
              WHERE product_id = ? AND status = 'active'
              ORDER BY batch_id DESC";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $item['product_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $batches = [];
    while ($batch = mysqli_fetch_assoc($result)) {
        $batches[] = $batch;
    }

    echo json_encode([
        'success' => true,
        'batches' => $batches
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
