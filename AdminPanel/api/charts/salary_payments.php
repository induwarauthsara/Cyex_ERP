<?php
/**
 * Salary Payments API Endpoint
 * Returns salary payments by employee
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$employees = [];
$amounts = [];

// Get salary payments grouped by employee
$sql = "SELECT 
            e.emp_name,
            SUM(s.amount) as total_paid
        FROM salary s
        INNER JOIN employees e ON s.emp_id = e.employ_id
        WHERE s.date BETWEEN '$startDate' AND '$endDate'
        GROUP BY s.emp_id, e.emp_name
        ORDER BY total_paid DESC
        LIMIT 10";

$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row['emp_name'];
        // Use absolute value to ensure positive display (salary might be stored as negative)
        $amounts[] = round(abs(floatval($row['total_paid'])), 2);
    }
}

// If no data
if (empty($employees)) {
    $employees = ['No Salary Payments'];
    $amounts = [0];
}

echo json_encode([
    'employees' => $employees,
    'amounts' => $amounts
]);
?>
