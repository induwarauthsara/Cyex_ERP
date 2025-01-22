<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

try {
    if (!isset($_GET['po_id'])) {
        throw new Exception('Purchase order ID is required');
    }

    $po_id = (int)$_GET['po_id'];

    // Get PO items with product details and received quantities
    $sql = "
        SELECT 
            poi.*,
            p.product_name,
            p.sku,
            COALESCE(SUM(gi.received_qty), 0) as received_qty
        FROM purchase_order_items poi
        JOIN products p ON poi.product_id = p.product_id
        LEFT JOIN grn_items gi ON poi.po_item_id = gi.po_item_id
        WHERE poi.po_id = ?
        GROUP BY poi.po_item_id
    ";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $po_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate remaining quantity
        $row['remaining_qty'] = $row['quantity'] - $row['received_qty'];
        if ($row['remaining_qty'] > 0) {
            $items[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();