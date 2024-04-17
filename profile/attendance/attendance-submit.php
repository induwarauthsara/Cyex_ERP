<?php
session_start();
require_once '../../inc/config.php';

if (isset($_SESSION['employee_id']) && isset($_GET['action']) || $_GET['action'] == 'Clock In' || $_GET['action'] == 'Clock Out') {
    $employee_id = $_SESSION['employee_id'];
    $action = $_GET['action'];

    // Insert data into the attendance table
    $sql = "INSERT INTO attendance (employee_id, action) VALUES ('$employee_id', '$action')";
    // echo $sql;
    if (mysqli_query($con, $sql)) {
        echo "Successfully inserted data into the database as $action.";
    } else {
        echo 'Error';
    }
} else {
    echo 'Error2';
}
