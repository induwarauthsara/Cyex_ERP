<?php
require_once 'config.php';

// Retrieve GET parameters and sanitize them
$customer_name = isset($_GET['name']) ? trim($_GET['name']) : '';
$customer_phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';

// Initialize the base SQL query
$sql = "SELECT customer_name, customer_mobile FROM customers WHERE 1=1";

// Use prepared statements to prevent SQL injection and improve performance
$params = [];
$types = '';

// Add conditions based on provided input
if (!empty($customer_name)) {
    $sql .= " AND customer_name LIKE ?";
    $params[] = '%' . $customer_name . '%';
    $types .= 's';
}

if (!empty($customer_phone)) {
    $sql .= " AND customer_mobile LIKE ?";
    $params[] = '%' . $customer_phone . '%';
    $types .= 's';
}

// Limit results to 5 for faster response
$sql .= " LIMIT 5";

$stmt = $con->prepare($sql);
if ($stmt) {
    // Bind parameters dynamically
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    while ($record = $result->fetch_assoc()) {
        $response[] = [
            'name' => $record['customer_name'],
            'phone' => $record['customer_mobile']
        ];
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);

    // Close the statement
    $stmt->close();
} else {
    // If statement preparation failed, send an error response
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error: ' . mysqli_error($con)]);
}

// Close the database connection
mysqli_close($con);