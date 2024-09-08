<?php
require_once '../inc/config.php';

$query = "SELECT `emp_name` FROM `employees` WHERE `status` = '1'";
$result = mysqli_query($con, $query);

$emp_name = [];
while ($row = mysqli_fetch_assoc($result)) {
    $emp_name[] = $row['emp_name'];
}

echo json_encode($emp_name);

mysqli_close($con);
