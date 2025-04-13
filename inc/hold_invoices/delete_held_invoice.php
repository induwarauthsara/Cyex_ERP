<?php
require_once '../config.php';  // Make sure this path is correct
header('Content-Type: application/json');

$id = $_GET['id'];

// Check if ID is provided
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'No invoice ID provided']);
    exit;
}

// Prepare SQL query
$sql = "UPDATE held_invoices SET status = 'completed' WHERE id = ?";
$stmt = mysqli_prepare($con, $sql);

// Check if prepare was successful
if ($stmt) {
    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Invoice successfully removed from Held Invoice list']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete invoice']);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
}

// Close the database connection
mysqli_close($con);
