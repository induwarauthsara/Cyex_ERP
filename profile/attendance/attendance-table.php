<?php
require_once '../../inc/config.php';
//SELECT `id`, `employee_id`, `action`, `date`, `time` FROM `attendance` WHERE 1

// Employee ID
if (!isset($_SESSION['employee_id'])) {
    session_start();
    $employee_id = $_SESSION['employee_id'];
} else {
    echo 'Error';
}

// Assuming you have a database connection established
$sql = "SELECT employees.emp_name, action, date, time 
        FROM attendance, employees
        WHERE attendance.employee_id = employees.employ_id
                AND employees.employ_id = '$employee_id'
        ORDER BY attendance.id DESC;";
$result = mysqli_query($con, $sql);

// Initialize an empty string to store the HTML for the todo list
$attendanceTableHTML = '';

// Check if the query was successful and fetch the rows
if ($result) {
    // Loop through the rows and generate HTML Table for each attendance record
    while ($row = mysqli_fetch_assoc($result)) {
        $attendanceTableHTML .= "<tr>
            <td>" . $row['emp_name'] . "</td>
            <td>" . $row['action'] . "</td>
            <td>" . $row['date'] . "</td>
            <td>" . $row['time'] . "</td>
        </tr>";
    }
}

// Return the generated HTML for the todo list
echo $attendanceTableHTML;
