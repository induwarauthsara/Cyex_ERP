<?php
// 1. Update Cash In Hand Amount

require_once '../../inc/config.php';

$new_cash_in_hand_amount = $_GET['new_cash_in_hand_amount'];

// 1. Update Cash In Hand Amount
$sql = "UPDATE accounts SET amount = '$new_cash_in_hand_amount' WHERE account_name = 'cash_in_hand';";

// Start output buffering to capture the output of insert_query
ob_start();
insert_query($sql, "Update Cash In Hand Amount to Rs.$new_cash_in_hand_amount", "Update Cash In Hand Amount Manually");
$output = ob_get_clean();

// Check if the output contains a success message or an error
if (strpos($output, 'successfully') !== false) {
    $response = [
        'status' => 'success',
        'message' => 'Cash in hand amount updated successfully.'
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Failed to update cash in hand amount.'
    ];
}

// Set the content type to JSON
header('Content-Type: application/json');
echo json_encode($response);
