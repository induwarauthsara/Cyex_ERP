<?php
require_once '../config.php';
header('Content-Type: application/json');

// Include individual_discount_mode in the SELECT query
$sql = "SELECT 
            id, 
            customer_name, 
            customer_number, 
            items, 
            total_amount, 
            discount_amount, 
            discount_type, 
            discount_value, 
            total_payable, 
            status, 
            individual_discount_mode,
            held_at 
        FROM held_invoices 
        WHERE status = 'held' 
        ORDER BY held_at DESC";

$result = mysqli_query($con, $sql);

$held_invoices = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Decode items JSON for easier handling in JavaScript
    $row['items'] = json_decode($row['items']);
    
    // Convert individual_discount_mode to boolean for JavaScript
    $row['individual_discount_mode'] = (bool)$row['individual_discount_mode'];
    
    $held_invoices[] = $row;
}

echo json_encode($held_invoices);

// Close connection
mysqli_close($con);
