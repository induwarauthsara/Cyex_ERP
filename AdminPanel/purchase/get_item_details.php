<?php
require_once('../../../inc/config.php');

header('Content-Type: application/json');

if (!isset($_GET['po_item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Purchase order item ID is required']);
    exit;
}

try {
    $query = "SELECT poi.*, p.product_name, p.barcode, p.sku,
              (SELECT batch_number 
               FROM product_batch 
               WHERE product_id = p.product_id 
               ORDER BY batch_id DESC 
               LIMIT 1) as last_batch_number
              FROM purchase_order_items poi
              JOIN products p ON poi.product_id = p.product_id
              WHERE poi.po_item_id = ?";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $_GET['po_item_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);

    if (!$item) {
        throw new Exception('Item not found');
    }

    echo json_encode([
        'success' => true,
        'item' => $item
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
