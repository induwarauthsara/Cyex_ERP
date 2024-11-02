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
$customer_name = $data['customer_name'];
$customer_number = $data['customer_number'];
$items = json_encode($data['items']);
$total_amount = $data['total_amount'];
$discount_amount = $data['discount_amount'];
$discount_type = $data['discount_type'];
$discount_value = $data['discount_value'];
$total_payable = $data['total_payable'];

// Prepare SQL query
$sql = "INSERT INTO held_invoices (customer_name, customer_number, items, total_amount, discount_amount, discount_type, discount_value, total_payable) 
        VALUES ('$customer_name', '$customer_number', '$items', $total_amount, $discount_amount, '$discount_type', $discount_value, $total_payable)";

// Execute the query
if (mysqli_query($con, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($con)]);
}

// Close connection
mysqli_close($con);
