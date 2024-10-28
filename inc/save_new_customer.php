<?php
require_once 'config.php';

// Retrieve and sanitize POST data
$customer_name = isset($_POST['name']) ? trim($_POST['name']) : '';
$customer_phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// Initialize response
$response = [];

// Validate input and perform length check to avoid unnecessary DB calls
if (empty($customer_name) || empty($customer_phone) || strlen($customer_phone) != 10) {
    // Return an error if name or phone is invalid
    $response['status'] = 'error';
    $response['message'] = 'Customer name and a 10-digit phone number are required.';
} else {
    // Prepare and execute the SQL to check for duplicate phone number
    $check_stmt = $con->prepare("SELECT 1 FROM customers WHERE customer_mobile = ?");
    $check_stmt->bind_param("s", $customer_phone);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // If a customer with the same phone number already exists, return an error
        $response['status'] = 'error';
        $response['message'] = 'A customer with this phone number already exists.';
    } else {
        // Insert the new customer using prepared statements
        $insert_stmt = $con->prepare("INSERT INTO customers (customer_name, customer_mobile) VALUES (?, ?)");
        $insert_stmt->bind_param("ss", $customer_name, $customer_phone);

        if ($insert_stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'New customer added successfully.';
            $response['customer'] = [
                'name' => $customer_name,
                'phone' => $customer_phone
            ];
        } else {
            // Return error if the insert fails
            $response['status'] = 'error';
            $response['message'] = 'Error adding customer: ' . $insert_stmt->error;
        }

        $insert_stmt->close(); // Close insert statement
    }

    $check_stmt->close(); // Close check statement
}

// Set content type to JSON and return the response immediately
header('Content-Type: application/json');
echo json_encode($response);

// Close database connection
mysqli_close($con);
