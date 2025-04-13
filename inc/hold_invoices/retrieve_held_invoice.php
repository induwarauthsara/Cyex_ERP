<?php
require_once '../config.php';  // Make sure this path is correct and config.php is included
header('Content-Type: application/json');

$id = $_GET['id'];

// Check if ID is provided
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'No invoice ID provided']);
    exit;
}

// Prepare SQL query to include individual_discount_mode
$sql = "SELECT 
            items, 
            discount_type, 
            discount_value, 
            customer_name, 
            customer_number, 
            individual_discount_mode 
        FROM held_invoices 
        WHERE id = ? AND status = 'held'";

$stmt = mysqli_prepare($con, $sql);

// Check if prepare was successful
if ($stmt) {
    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    // Bind the result variables
    mysqli_stmt_bind_result(
        $stmt, 
        $items, 
        $discount_type, 
        $discount_value, 
        $customer_name, 
        $customer_number, 
        $individual_discount_mode
    );
    mysqli_stmt_fetch($stmt);

    if ($items) {
        // Respond with items, discount_type, discount_value, customer_name, customer_number, and individual_discount_mode
        echo json_encode([
            'success' => true,
            'items' => json_decode($items),
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'customer_name' => $customer_name,
            'customer_number' => $customer_number,
            'individual_discount_mode' => (bool)$individual_discount_mode
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invoice not found']);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
}

// Close the database connection
mysqli_close($con);
