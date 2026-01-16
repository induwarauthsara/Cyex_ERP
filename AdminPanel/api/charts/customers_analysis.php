<?php
/**
 * Customers Credit Analysis API Endpoint
 * Returns customer credit data and top customers
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

// Get top customers by outstanding credit
$customers = [];
$creditAmounts = [];

$sql = "SELECT 
            c.customer_name,
            SUM(i.balance) as total_credit
        FROM invoice i
        INNER JOIN customers c ON i.customer_id = c.customer_id
        WHERE i.balance > 0 
        AND i.is_deleted = 0
        GROUP BY i.customer_id, c.customer_name
        ORDER BY total_credit DESC
        LIMIT 10";

$result = @mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customerName = strlen($row['customer_name']) > 20 
            ? substr($row['customer_name'], 0, 17) . '...' 
            : $row['customer_name'];
        $customers[] = $customerName;
        $creditAmounts[] = round(floatval($row['total_credit']), 2);
    }
}

// Get top customers by purchase volume in the selected period
$topCustomers = [];
$purchaseVolumes = [];

$topSql = "SELECT 
            c.customer_name,
            SUM(i.total) as total_purchases,
            COUNT(i.invoice_number) as invoice_count
        FROM invoice i
        INNER JOIN customers c ON i.customer_id = c.customer_id
        WHERE i.invoice_date BETWEEN '$startDate' AND '$endDate'
        AND i.is_deleted = 0
        GROUP BY i.customer_id, c.customer_name
        ORDER BY total_purchases DESC
        LIMIT 10";

$topResult = @mysqli_query($con, $topSql);

if ($topResult && mysqli_num_rows($topResult) > 0) {
    while ($row = mysqli_fetch_assoc($topResult)) {
        $customerName = strlen($row['customer_name']) > 20 
            ? substr($row['customer_name'], 0, 17) . '...' 
            : $row['customer_name'];
        $topCustomers[] = $customerName;
        $purchaseVolumes[] = round(floatval($row['total_purchases']), 2);
    }
}

// If no credit data
if (empty($customers)) {
    $customers = ['No Outstanding Credit'];
    $creditAmounts = [0];
}

// If no purchase data
if (empty($topCustomers)) {
    $topCustomers = ['No Purchases'];
    $purchaseVolumes = [0];
}

echo json_encode([
    'credit' => [
        'customers' => $customers,
        'amounts' => $creditAmounts
    ],
    'top_buyers' => [
        'customers' => $topCustomers,
        'volumes' => $purchaseVolumes
    ]
]);
?>
