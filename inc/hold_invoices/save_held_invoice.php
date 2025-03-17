<?php
require_once '../config.php';
header('Content-Type: application/json');

// Get JSON input data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Retrieve data from JSON
$customer_name = mysqli_real_escape_string($con, $data['customer_name']);
$customer_number = mysqli_real_escape_string($con, $data['customer_number']);
$items = json_encode($data['items']);
$total_amount = floatval($data['total_amount']);
$discount_amount = floatval($data['discount_amount']);
$discount_type = mysqli_real_escape_string($con, $data['discount_type']);
$discount_value = floatval($data['discount_value']);
$total_payable = floatval($data['total_payable']);

// Get individual discount mode (convert to 0 or 1 for database)
$individual_discount_mode = isset($data['individual_discount_mode']) && $data['individual_discount_mode'] ? 1 : 0;

// Prepare SQL query with individual_discount_mode field
$sql = "INSERT INTO held_invoices (
            customer_name, 
            customer_number, 
            items, 
            total_amount, 
            discount_amount, 
            discount_type, 
            discount_value, 
            total_payable, 
            individual_discount_mode
        ) 
        VALUES (
            '$customer_name', 
            '$customer_number', 
            '$items', 
            $total_amount, 
            $discount_amount, 
            '$discount_type', 
            $discount_value, 
            $total_payable, 
            $individual_discount_mode
        )";

// Execute the query
if (mysqli_query($con, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($con)]);
}

// Close connection
mysqli_close($con);
