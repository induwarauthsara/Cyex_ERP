<?php
require_once '../inc/config.php';
header('Content-Type: application/json');

$employeesOptionList = [];

$query = "SELECT `emp_name` FROM `employees` WHERE `status` = '1';";
if (isset($con)) {
    $result = mysqli_query($con, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $employeesOptionList[] = $row['emp_name'];
        }
        echo json_encode(['success' => true, 'employees' => $employeesOptionList]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to fetch employees']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Database connection error']);
}
