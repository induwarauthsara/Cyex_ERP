<?php
header('Content-Type: application/json');
require_once 'config.php';

// Check if user is logged in (assuming session management is in config.php)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit;
}

try {
    // Get the latest invoice ID
    $query = "SELECT invoice_number FROM invoice ORDER BY invoice_number DESC LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode([
            'success' => true,
            'invoice_id' => $row['invoice_number']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No invoices found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving last invoice: ' . $e->getMessage()
    ]);
}
?> 