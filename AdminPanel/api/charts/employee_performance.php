<?php
/**
 * Employee Performance API Endpoint
 * Returns employee sales and commission data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$employees = [];

// Get employee performance based on sales (using biller or worker)
$sql = "SELECT 
            i.biller as name,
            SUM(i.advance) as sales,
            SUM(i.profit) as profit
        FROM invoice i
        WHERE i.invoice_date BETWEEN '$startDate' AND '$endDate'
        AND i.is_deleted = 0
        AND i.biller != ''
        GROUP BY i.biller
        ORDER BY sales DESC
        LIMIT 10";

$result = mysqli_query($con, $sql);
$billerData = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $billerData[$row['name']] = [
            'sales' => floatval($row['sales']),
            'profit' => floatval($row['profit'])
        ];
    }
}

// Get commission data
$commissionSql = "SELECT 
                    e.emp_name as name,
                    SUM(ech.commission_amount) as commission
                  FROM employee_commission_history ech
                  INNER JOIN employees e ON ech.employee_id = e.employ_id
                  WHERE DATE(ech.created_at) BETWEEN '$startDate' AND '$endDate'
                  GROUP BY ech.employee_id, e.emp_name";

$commissionResult = mysqli_query($con, $commissionSql);
$commissionData = [];

if ($commissionResult && mysqli_num_rows($commissionResult) > 0) {
    while ($row = mysqli_fetch_assoc($commissionResult)) {
        $commissionData[$row['name']] = floatval($row['commission']);
    }
}

// Merge data
foreach ($billerData as $name => $data) {
    $employees[] = [
        'name' => $name,
        'sales' => round($data['sales'], 2),
        'commission' => round($commissionData[$name] ?? 0, 2)
    ];
}

// Sort by sales
usort($employees, function($a, $b) {
    return $b['sales'] <=> $a['sales'];
});

// Limit to top 10
$employees = array_slice($employees, 0, 10);

echo json_encode([
    'employees' => $employees
]);
?>
