<?php
ob_start();

require_once('../nav.php');
ob_clean();

header('Content-Type: application/json');

try {
    if (!isset($_GET['product_id'])) {
        throw new Exception('Product ID is required');
    }

    $product_id = (int)$_GET['product_id'];

    // Get active batches for the product
    $sql = "
            SELECT
            batch_id,
            batch_number,
            cost,
            selling_price,
            expiry_date,
            quantity,
            status
            FROM product_batch
            WHERE product_id = ?
            AND status = 'active'
            AND quantity > 0
            ORDER BY created_at DESC
            ";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $batches = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['expiry_date'] = $row['expiry_date'] ? date('Y-m-d', strtotime($row['expiry_date'])) : null;
        $batches[] = $row;
    }

    echo json_encode([
        'success' => true,
        'batches' => $batches
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();
