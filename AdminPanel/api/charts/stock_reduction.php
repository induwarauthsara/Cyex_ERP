<?php
/**
 * Stock Reduction API Endpoint
 * Returns top products by stock reduction
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$products = [];
$quantities = [];

// Get top products by quantity sold (stock reduction)
$sql = "SELECT 
            s.product,
            SUM(CAST(s.qty AS DECIMAL(10,3))) as qty_sold
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
        // Truncate long product names
        $productName = strlen($row['product']) > 25 
            ? substr($row['product'], 0, 22) . '...' 
            : $row['product'];
        $products[] = $productName;
        $quantities[] = round(floatval($row['qty_sold']), 2);
    }
}

// If no data
if (empty($products)) {
    $products = ['No Stock Movement'];
    $quantities = [0];
}

echo json_encode([
    'products' => $products,
    'quantities' => $quantities
]);
?>
