<?php
require_once('../../inc/config.php');
header('Content-Type: application/json');

// Check if return ID is provided
if (!isset($_GET['return_id']) || empty($_GET['return_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Return ID is required'
    ]);
    exit;
}

$return_id = intval($_GET['return_id']);

// Fetch return details - using simpler query to avoid column name issues
$return_query = "SELECT sr.* FROM sales_returns sr WHERE sr.return_id = $return_id";
$return_data = fetch_data($return_query);

if (empty($return_data)) {
    echo json_encode([
        'success' => false,
        'message' => 'Return not found'
    ]);
    exit;
}

$return = $return_data[0];

// Get invoice number separately
$invoice_number = '';
if (!empty($return['invoice_id'])) {
    $invoice_data = fetch_data("SELECT invoice_number FROM invoice WHERE invoice_number = '{$return['invoice_id']}'");
    if (!empty($invoice_data)) {
        $invoice_number = $invoice_data[0]['invoice_number'];
    }
}

// Get customer name separately
$customer_name = 'Walk-in Customer';
if (!empty($return['customer_id'])) {
    $customer_data = fetch_data("SELECT * FROM customers WHERE customer_id = '{$return['customer_id']}'");
    if (!empty($customer_data)) {
        $customer_name = isset($customer_data[0]['name']) ? $customer_data[0]['name'] : 
                       (isset($customer_data[0]['customer_name']) ? $customer_data[0]['customer_name'] : 'Customer #'.$return['customer_id']);
    }
}

// Get employee name separately
$employee_name = 'System';
if (!empty($return['user_id'])) {
    $employee_data = fetch_data("SELECT * FROM employees WHERE employ_id = '{$return['user_id']}'");
    if (!empty($employee_data)) {
        $employee_name = isset($employee_data[0]['emp_name']) ? $employee_data[0]['emp_name'] : 
                       (isset($employee_data[0]['employee_name']) ? $employee_data[0]['employee_name'] : 'Employee #'.$return['user_id']);
    }
}

// Get return items
$items = [];
$items_query = "SELECT sri.* FROM sales_return_items sri WHERE sri.return_id = $return_id";
$items_data = fetch_data($items_query);

// Enhance item data with product and batch information
foreach ($items_data as $item) {
    $product_data = [];
    if (!empty($item['product_id'])) {
        $product_data = fetch_data("SELECT * FROM products WHERE product_id = '{$item['product_id']}'");
    }
    
    $batch_data = [];
    if (!empty($item['batch_id'])) {
        $batch_data = fetch_data("SELECT * FROM product_batch WHERE batch_id = '{$item['batch_id']}'");
    }
    
    $item_name = isset($product_data[0]['name']) ? $product_data[0]['name'] : 
               (isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : 'Product #'.$item['product_id']);
    
    $batch_number = isset($batch_data[0]['batch_number']) ? $batch_data[0]['batch_number'] : 'N/A';
    
    $items[] = array_merge($item, [
        'item_name' => $item_name,
        'batch_number' => $batch_number
    ]);
}

// Format response
$response = [
    'success' => true,
    'return' => [
        'return_id' => $return['return_id'],
        'invoice_id' => $return['invoice_id'],
        'invoice_number' => $invoice_number,
        'customer_id' => $return['customer_id'],
        'customer_name' => $customer_name,
        'return_date' => $return['return_date'],
        'return_amount' => $return['return_amount'],
        'refund_method' => $return['refund_method'],
        'return_reason' => $return['return_reason'],
        'return_note' => $return['return_note'],
        'user_id' => $return['user_id'],
        'employee_name' => $employee_name,
        'items' => $items
    ]
];

echo json_encode($response);
?>