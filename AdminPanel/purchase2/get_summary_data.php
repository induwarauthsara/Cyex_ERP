<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

try {
    // Get total number of POs
    $total_po_sql = "SELECT COUNT(*) as total FROM purchase_orders";
    $total_po_result = mysqli_query($con, $total_po_sql);
    $total_po = mysqli_fetch_assoc($total_po_result)['total'];

    // Get pending GRNs count
    $pending_grn_sql = "SELECT COUNT(*) as pending FROM purchase_orders WHERE status = 'pending'";
    $pending_grn_result = mysqli_query($con, $pending_grn_sql);
    $pending_grn = mysqli_fetch_assoc($pending_grn_result)['pending'];

    // Get total amount for current month
    $total_amount_sql = "
        SELECT COALESCE(SUM(total_amount), 0) as total 
        FROM purchase_orders 
        WHERE YEAR(order_date) = YEAR(CURDATE()) 
        AND MONTH(order_date) = MONTH(CURDATE())
    ";
    $total_amount_result = mysqli_query($con, $total_amount_sql);
    $total_amount = mysqli_fetch_assoc($total_amount_result)['total'];

    // Get outstanding payments
    $outstanding_sql = "
        SELECT 
            COALESCE(SUM(po.total_amount - COALESCE(sp.paid_amount, 0)), 0) as outstanding
        FROM purchase_orders po
        LEFT JOIN (
            SELECT po_id, SUM(amount) as paid_amount 
            FROM supplier_payments 
            GROUP BY po_id
        ) sp ON po.po_id = sp.po_id
        WHERE po.status != 'draft'
    ";
    $outstanding_result = mysqli_query($con, $outstanding_sql);
    $outstanding_amount = mysqli_fetch_assoc($outstanding_result)['outstanding'];

    echo json_encode([
        'totalPO' => $total_po,
        'pendingGRN' => $pending_grn,
        'totalAmount' => $total_amount,
        'outstandingAmount' => $outstanding_amount
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();