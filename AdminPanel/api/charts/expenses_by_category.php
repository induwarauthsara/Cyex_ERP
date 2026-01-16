<?php
/**
 * Expenses by Category API Endpoint
 * Returns expense breakdown by category for pie/doughnut chart
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$labels = [];
$values = [];
$colors = [];

// Get expenses grouped by category
$sql = "SELECT 
            ec.category_name,
            ec.color_code,
            SUM(e.amount) as total_amount
        FROM expenses e
        INNER JOIN expense_categories ec ON e.category_id = ec.category_id
        WHERE DATE(e.expense_date) BETWEEN '$startDate' AND '$endDate'
        GROUP BY e.category_id, ec.category_name, ec.color_code
        ORDER BY total_amount DESC
        LIMIT 8";

$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['category_name'];
        $values[] = round(floatval($row['total_amount']), 2);
        $colors[] = $row['color_code'] ?? '#6366f1';
    }
}

// Add salary as a category
$salarySql = "SELECT SUM(amount) as total_salary 
              FROM salary 
              WHERE date BETWEEN '$startDate' AND '$endDate'";
$salaryResult = mysqli_query($con, $salarySql);
$salaryData = mysqli_fetch_assoc($salaryResult);

if ($salaryData && floatval($salaryData['total_salary']) > 0) {
    $labels[] = 'Salary Payments';
    $values[] = round(floatval($salaryData['total_salary']), 2);
    $colors[] = '#f59e0b';
}

// Add petty cash as a category
$pettySql = "SELECT SUM(amount) as total_petty 
             FROM pettycash 
             WHERE date BETWEEN '$startDate' AND '$endDate'";
$pettyResult = mysqli_query($con, $pettySql);
$pettyData = mysqli_fetch_assoc($pettyResult);

if ($pettyData && floatval($pettyData['total_petty']) > 0) {
    $labels[] = 'Petty Cash';
    $values[] = round(floatval($pettyData['total_petty']), 2);
    $colors[] = '#ec4899';
}

// If no data, return sample
if (empty($labels)) {
    $labels = ['No Expenses'];
    $values = [0];
    $colors = ['#64748b'];
}

echo json_encode([
    'labels' => $labels,
    'values' => $values,
    'colors' => $colors
]);
?>
