<?php
/**
 * Best Selling Products API Endpoint
 * Returns top selling products by quantity and revenue
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$products = [];

// Get best selling products
$sql = "SELECT 
            s.product as name,
            SUM(CAST(s.qty AS DECIMAL(10,3))) as qty_sold,
            SUM(s.amount) as revenue
        FROM sales s
        INNER JOIN invoice i ON s.invoice_number = i.invoice_number
        WHERE i.invoice_date BETWEEN '$startDate' AND '$endDate'
        AND i.is_deleted = 0
        GROUP BY s.product
        ORDER BY qty_sold DESC
        LIMIT 10";

$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            'name' => $row['name'],
            'qty_sold' => round(floatval($row['qty_sold']), 2),
            'revenue' => round(floatval($row['revenue']), 2)
        ];
    }
}

echo json_encode([
    'products' => $products
]);
?>
