<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Build query to get products with latest batch and purchase information
    $where_clause = $search ?
        "WHERE p.product_name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?" : "";

    $sql = "
        SELECT 
            p.product_id as id,
            p.product_name,
            p.sku,
            COALESCE(pb.cost, 0) as last_cost,
            COALESCE(pb.quantity, 0) as current_stock,
            p.stock_alert_limit
        FROM products p
        LEFT JOIN (
            SELECT 
                product_id,
                cost,
                quantity
            FROM product_batch
            WHERE batch_id IN (
                SELECT MAX(batch_id)
                FROM product_batch
                GROUP BY product_id
            )
        ) pb ON p.product_id = pb.product_id
        $where_clause
        ORDER BY p.product_name
        LIMIT ? OFFSET ?
    ";

    // Prepare and execute query
    $stmt = mysqli_prepare($con, $sql);
    if ($search) {
        $search_param = "%$search%";
        mysqli_stmt_bind_param($stmt, 'sssii', $search_param, $search_param, $search_param, $limit, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Format text to show product details and stock status
        $text = $row['product_name'];
        if ($row['sku']) {
            $text .= " (SKU: {$row['sku']})";
        }
        $text .= " - Last Cost: " . number_format($row['last_cost'], 2);

        // Add stock warning if below alert limit
        if ($row['current_stock'] <= $row['stock_alert_limit']) {
            $text .= " ⚠️ Low Stock: {$row['current_stock']} units";
        }

        $items[] = [
            'id' => $row['id'],
            'text' => $text,
            'last_cost' => $row['last_cost']
        ];
    }

    // Count total for pagination
    $count_sql = "SELECT COUNT(*) as total FROM products p " . $where_clause;
    if ($search) {
        $count_stmt = mysqli_prepare($con, $count_sql);
        mysqli_stmt_bind_param($count_stmt, 'sss', $search_param, $search_param, $search_param);
        mysqli_stmt_execute($count_stmt);
    } else {
        $count_stmt = mysqli_query($con, $count_sql);
    }
    $total = mysqli_fetch_assoc($count_stmt)['total'];

    echo json_encode([
        'items' => $items,
        'hasMore' => ($page * $limit) < $total
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();
