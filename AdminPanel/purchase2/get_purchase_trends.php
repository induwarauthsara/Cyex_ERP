<?php
ob_start();

require_once('../nav.php');
ob_clean();

header('Content-Type: application/json');

try {
    // Get monthly purchase trends for the last 12 months
    $trends_sql = "
        SELECT 
            DATE_FORMAT(order_date, '%Y-%m') as month,
            SUM(total_amount) as total
        FROM purchase_orders
        WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        AND status != 'draft'
        GROUP BY DATE_FORMAT(order_date, '%Y-%m')
        ORDER BY month ASC
    ";

    $trends_result = mysqli_query($con, $trends_sql);
    if (!$trends_result) {
        throw new Exception(mysqli_error($con));
    }

    $labels = [];
    $data = [];

    while ($row = mysqli_fetch_assoc($trends_result)) {
        $month_date = new DateTime($row['month'] . '-01');
        $labels[] = $month_date->format('M Y');
        $data[] = (float)$row['total'];
    }

    echo json_encode([
        'labels' => $labels,
        'data' => $data
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();
