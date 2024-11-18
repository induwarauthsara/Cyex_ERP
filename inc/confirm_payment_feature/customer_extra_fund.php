<?php
// Include database connection
include '../config.php'; // Replace with your actual database connection file

// Get raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate and sanitize customerPhone
if (!isset($data['customerPhone']) || empty($data['customerPhone'])) {
    http_response_code(400); // Bad Request
    echo "0.00"; // Default value
    exit;
}

// Sanitize customerPhone (numeric only)
$customerPhone = preg_replace('/[^0-9]/', '', $data['customerPhone']);

// Validate phone number length (optional)
if (strlen($customerPhone) < 10 || strlen($customerPhone) > 15) {
    http_response_code(400); // Bad Request
    echo "0.00"; // Invalid phone number
    exit;
}

// Prepare and execute the query
$query = "SELECT customer_extra_fund FROM customers WHERE customer_mobile = ?";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    // Bind the parameter
    mysqli_stmt_bind_param($stmt, 's', $customerPhone);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Bind result variable
    mysqli_stmt_bind_result($stmt, $customerExtraFund);

    // Fetch the result
    if (mysqli_stmt_fetch($stmt)) {
        echo number_format($customerExtraFund, 2, '.', ''); // Return the extra fund formatted to 2 decimals
    } else {
        echo "0.00"; // Default value if no record found
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    http_response_code(500); // Internal Server Error
    echo "Query preparation failed.";
}

// Close the connection
mysqli_close($con);
?>