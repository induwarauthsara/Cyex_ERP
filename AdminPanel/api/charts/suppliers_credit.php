<?php
/**
 * Suppliers Credit Balance API Endpoint
 * Returns top suppliers by outstanding credit balance
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$suppliers = [];
$balances = [];
$colors = [];

// Get top suppliers by credit balance
$sql = "SELECT 
            supplier_name,
            credit_balance
        FROM suppliers
        WHERE credit_balance > 0
        ORDER BY credit_balance DESC
        LIMIT 10";

$result = @mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $colorPalette = ['#ef4444', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9', '#3b82f6'];
    $index = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = strlen($row['supplier_name']) > 20 
            ? substr($row['supplier_name'], 0, 17) . '...' 
            : $row['supplier_name'];
        $balances[] = round(floatval($row['credit_balance']), 2);
        $colors[] = $colorPalette[$index % count($colorPalette)];
        $index++;
    }
}

// If no data
if (empty($suppliers)) {
    $suppliers = ['No Credit Balance'];
    $balances = [0];
    $colors = ['#64748b'];
}

echo json_encode([
    'suppliers' => $suppliers,
    'balances' => $balances,
    'colors' => $colors
]);
?>
