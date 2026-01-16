<?php
/**
 * Product Sales Trend API Endpoint
 * Returns product sales over time for line chart
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$labels = [];
$values = [];

$dateDiff = (strtotime($endDate) - strtotime($startDate)) / 86400;

// Determine grouping based on date range
if ($dateDiff > 90) {
    $dateFormat = '%Y-%m';
    $displayFormat = 'M Y';
} else if ($dateDiff > 14) {
    $dateFormat = '%Y-%u';
    $displayFormat = 'Week';
} else {
    $dateFormat = '%Y-%m-%d';
    $displayFormat = 'M d';
}

$sql = "SELECT 
            DATE_FORMAT(i.invoice_date, '$dateFormat') as period,
            SUM(CAST(s.qty AS DECIMAL(10,3))) as qty_sold
        FROM sales s
        INNER JOIN invoice i ON s.invoice_number = i.invoice_number
        WHERE i.invoice_date BETWEEN '$startDate' AND '$endDate'
        AND i.is_deleted = 0
        GROUP BY period
        ORDER BY i.invoice_date";

$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Format label
        if ($dateDiff <= 14) {
            $labels[] = date('M d', strtotime($row['period']));
        } else if ($dateDiff <= 90) {
            $parts = explode('-', $row['period']);
            $labels[] = 'W' . $parts[1];
        } else {
            $labels[] = date('M Y', strtotime($row['period'] . '-01'));
        }
        $values[] = round(floatval($row['qty_sold']), 1);
    }
}

// If no data
if (empty($labels)) {
    $labels = ['No Data'];
    $values = [0];
}

echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
?>
