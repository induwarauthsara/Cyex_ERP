<?php
/**
 * Get GRN Details - Simple and Reliable Version
 * Fetch comprehensive GRN information for display purposes
 */

// Include database configuration
require_once('../../inc/config.php');

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response
$response = array();

// Get GRN ID from request
$grn_id = isset($_GET['grn_id']) ? (int)$_GET['grn_id'] : 0;

// Validate GRN ID
if ($grn_id <= 0) {
    $response = array(
        'success' => false,
        'error' => 'Valid GRN ID is required'
    );
    echo json_encode($response);
    exit;
}

// Check database connection
if (!$con) {
    $response = array(
        'success' => false,
        'error' => 'Database connection failed'
    );
    echo json_encode($response);
    exit;
}

// Query to get GRN details
$grn_query = "SELECT 
    grn.*,
    po.po_number,
    po.order_date,
    s.supplier_name,
    s.supplier_tel,
    s.supplier_address,
    e.emp_name as created_by_name
FROM goods_receipt_notes grn
LEFT JOIN purchase_orders po ON grn.po_id = po.po_id  
LEFT JOIN suppliers s ON grn.supplier_id = s.supplier_id
LEFT JOIN employees e ON grn.created_by = e.employ_id
WHERE grn.grn_id = $grn_id";

$grn_result = mysqli_query($con, $grn_query);

if (!$grn_result) {
    $response = array(
        'success' => false,
        'error' => 'Database query failed: ' . mysqli_error($con)
    );
    echo json_encode($response);
    exit;
}

if (mysqli_num_rows($grn_result) == 0) {
    $response = array(
        'success' => false,
        'error' => 'GRN not found'
    );
    echo json_encode($response);
    exit;
}

// Get GRN data
$grn_data = mysqli_fetch_assoc($grn_result);

// Query to get GRN items
$items_query = "SELECT 
    gi.*,
    p.product_name,
    p.barcode,
    p.sku,
    p.description,
    pb.batch_number,
    (gi.received_qty * gi.cost) as total_cost
FROM grn_items gi
LEFT JOIN product_batch pb ON gi.batch_id = pb.batch_id
LEFT JOIN products p ON pb.product_id = p.product_id
WHERE gi.grn_id = $grn_id
ORDER BY gi.grn_item_id";

$items_result = mysqli_query($con, $items_query);

if (!$items_result) {
    $response = array(
        'success' => false,
        'error' => 'Failed to fetch GRN items: ' . mysqli_error($con)
    );
    echo json_encode($response);
    exit;
}

// Collect items data
$items = array();
$total_amount = 0;

while ($item = mysqli_fetch_assoc($items_result)) {
    $item['total_cost'] = $item['received_qty'] * $item['cost'];
    $total_amount += $item['total_cost'];
    $items[] = $item;
}

// Add items to GRN data
$grn_data['items'] = $items;
$grn_data['calculated_total'] = $total_amount;

// Set payment information from GRN table
$grn_data['paid_amount'] = isset($grn_data['paid_amount']) ? (float)$grn_data['paid_amount'] : 0;
$grn_data['total_amount'] = isset($grn_data['total_amount']) ? (float)$grn_data['total_amount'] : $total_amount;
$grn_data['outstanding_amount'] = $grn_data['total_amount'] - $grn_data['paid_amount'];

// Determine payment status
if ($grn_data['paid_amount'] >= $grn_data['total_amount']) {
    $grn_data['payment_status'] = 'paid';
} elseif ($grn_data['paid_amount'] > 0) {
    $grn_data['payment_status'] = 'partial';
} else {
    $grn_data['payment_status'] = 'unpaid';
}

// Add summary information
$grn_data['item_count'] = count($items);

// Prepare successful response
$response = array(
    'success' => true,
    'data' => $grn_data,
    'message' => 'GRN details retrieved successfully'
);

// Output JSON response
echo json_encode($response);

// Close database connection
mysqli_close($con);
?>