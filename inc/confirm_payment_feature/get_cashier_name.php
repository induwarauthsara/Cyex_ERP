<?php
session_start();

// Check if the session variables are set
if (isset($_SESSION['employee_name']) && isset($_SESSION['employee_id']) && isset($_SESSION['employee_role'])) {
    // Retrieve session data
    $employee_name = $_SESSION['employee_name'];
    $employee_id = $_SESSION['employee_id'];
    $employee_role = $_SESSION['employee_role'];

    // Return data in JSON format
    echo json_encode([
        "success" => true,
        "name" => $employee_name,
        "id" => $employee_id,
        "role" => $employee_role
    ]);
} else {
    // Return error if session data is missing
    echo json_encode([
        "success" => false,
        "message" => "Employee data not found in session"
    ]);
}
