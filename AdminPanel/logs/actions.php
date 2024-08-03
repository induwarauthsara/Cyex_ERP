<?php

include '../nav.php';

echo "<h1>Action List</h1>";

$sql = "SELECT action_log.*, employees.emp_name FROM `action_log` INNER JOIN employees WHERE employees.employ_id = action_log.employee_id ORDER BY `action_log`.`action_id` DESC";

$result = mysqli_query($con, $sql);
$resultCheck = mysqli_num_rows($result);
if($resultCheck > 0){

    // action_id, employee_id, action, description, date, time
    echo "<table id='DataTable'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>id</th>";
    echo "<th>EMP</th>";
    echo "<th>action</th>";
    echo "<th>description</th>";
    echo "<th>Date</th>";
    echo "<th>Time</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $row['action_id'] . "</td>";
        echo "<td>" . $row['emp_name'] . "</td>";
        echo "<td>" . $row['action'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['time'] . "</td>";
        echo "</tr>";
    }

}