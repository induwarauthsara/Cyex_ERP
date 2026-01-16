<?php
/**
 * GRN (Goods Received Notes) API Endpoint
 * Returns GRN statistics and trends
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$labels = [];
$values = [];
$totals = [];

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

// Get GRN data grouped by date
$sql = "SELECT 
            DATE_FORMAT(grn_date, '$dateFormat') as period,
            COUNT(*) as grn_count,
            SUM(total_amount) as grn_total
        FROM grn
        WHERE grn_date BETWEEN '$startDate' AND '$endDate'
        GROUP BY period
        ORDER BY grn_date";

$result = @mysqli_query($con, $sql);

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
        $values[] = intval($row['grn_count']);
        $totals[] = round(floatval($row['grn_total']), 2);
    }
}

// If no data
if (empty($labels)) {
    $labels = ['No Data'];
    $values = [0];
    $totals = [0];
}

echo json_encode([
    'labels' => $labels,
    'counts' => $values,
    'totals' => $totals
]);
?>
