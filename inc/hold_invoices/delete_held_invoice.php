<?php
require_once '../config.php';  // Make sure this path is correct
header('Content-Type: application/json');

$id = $_GET['id'];

// Check if ID is provided
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'No invoice ID provided']);
    exit;
}

// Prepare SQL query to delete the invoice
$sql = "DELETE FROM held_invoices WHERE id = ?";
$stmt = mysqli_prepare($con, $sql);

if ($stmt) {
    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Invoice deleted successfully']);
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
