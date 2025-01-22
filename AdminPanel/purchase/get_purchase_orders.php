<?php
// Turn off output buffering
ob_start();
require_once('../nav.php');
ob_clean();

header('Content-Type: application/json');

try {
    $query = "SELECT 
                po.*,
                s.supplier_name,
                COALESCE(
                    (SELECT SUM(amount) 
                     FROM supplier_payments 
                     WHERE supplier_id = po.supplier_id AND 
                           reference_no = po.po_number
                    ), 0
                ) as paid_amount
              FROM purchase_orders po
              JOIN suppliers s ON po.supplier_id = s.supplier_id
              ORDER BY po.order_date DESC";

    $result = mysqli_query($con, $query);

    if (!$result) {
        throw new Exception(mysqli_error($con));
    }

    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        // Format dates
        $row['order_date'] = date('Y-m-d', strtotime($row['order_date']));
        if ($row['delivery_date']) {
            $row['delivery_date'] = date('Y-m-d', strtotime($row['delivery_date']));
        }

        $orders[] = $row;
    }

    echo json_encode($orders);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
ob_end_flush();
