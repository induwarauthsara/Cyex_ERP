<?php
/**
 * Fast Moving Products API Endpoint
 * Returns products with high sales velocity
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$products = [];

// Calculate number of days in range
$dateDiff = max(1, (strtotime($endDate) - strtotime($startDate)) / 86400);

// Get product sales data
$sql = "SELECT 
            p.product_id,
            p.product_name as name,
            c.category_name as category,
            SUM(CAST(s.qty AS DECIMAL(10,3))) as sold,
            (SELECT SUM(pb.quantity) FROM product_batch pb WHERE pb.product_id = p.product_id) as current_stock
        FROM sales s
        INNER JOIN invoice i ON s.invoice_number = i.invoice_number
        LEFT JOIN products p ON s.product = p.product_name
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE i.invoice_date BETWEEN '$startDate' AND '$endDate'
        AND i.is_deleted = 0
        GROUP BY p.product_id, p.product_name, c.category_name
        ORDER BY sold DESC
        LIMIT 15";

$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $sold = floatval($row['sold']);
        $currentStock = floatval($row['current_stock'] ?? 0);
        $initialStock = $sold + $currentStock;
        $velocity = $sold / $dateDiff; // Items per day
        $daysToDeplete = $velocity > 0 ? ceil($currentStock / $velocity) : 0;
        
        $products[] = [
            'name' => $row['name'] ?? 'Unknown Product',
            'category' => $row['category'] ?? 'Uncategorized',
            'initial_stock' => round($initialStock, 2),
            'sold' => round($sold, 2),
            'current_stock' => round($currentStock, 2),
            'velocity' => round($velocity, 2),
            'days_to_deplete' => $daysToDeplete
        ];
    }
}

// Sort by velocity (descending)
usort($products, function($a, $b) {
    return $b['velocity'] <=> $a['velocity'];
});

// Limit to top 10
$products = array_slice($products, 0, 10);

echo json_encode([
    'products' => $products
]);
?>
