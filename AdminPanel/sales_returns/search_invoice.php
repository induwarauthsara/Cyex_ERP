<?php
require_once('../../inc/config.php');
header('Content-Type: application/json');

// Check if invoice number is provided via POST or GET
$invoice_number = '';
$bypass_return_window = false;

if (isset($_POST['invoice_number']) && !empty($_POST['invoice_number'])) {
    $invoice_number = $_POST['invoice_number'];
    $bypass_return_window = isset($_POST['bypass_return_window']) && $_POST['bypass_return_window'] == 'true';
} elseif (isset($_GET['invoice']) && !empty($_GET['invoice'])) {
    $invoice_number = $_GET['invoice'];
    $bypass_return_window = isset($_GET['bypass']) && $_GET['bypass'] == 'true';
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invoice number is required'
    ]);
    exit;
}

// First, fetch basic invoice details without depending on column names in customers table
$invoice_query = "SELECT i.* FROM invoice i WHERE i.invoice_number = '$invoice_number'";
$invoice_data = fetch_data($invoice_query);

if (empty($invoice_data)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invoice not found'
    ]);
    exit;
}

$invoice = $invoice_data[0];

// Get customer details separately if needed
$customer_name = 'Walk-in Customer';
$customer_id = null;
if (!empty($invoice['customer_id'])) {
    $customer_query = "SELECT * FROM customers WHERE id = '{$invoice['customer_id']}'";
    $customer_data = fetch_data($customer_query);
    
    if (!empty($customer_data)) {
        $customer_id = $customer_data[0]['id'];
        // Try different possible column names for customer name
        if (isset($customer_data[0]['customer_name'])) {
            $customer_name = $customer_data[0]['customer_name'];
        } elseif (isset($customer_data[0]['name'])) {
            $customer_name = $customer_data[0]['name'];
        } elseif (isset($customer_data[0]['full_name'])) {
            $customer_name = $customer_data[0]['full_name'];
        }
    }
}

// Check if invoice is within return period (7 days)
$invoice_date = new DateTime($invoice['invoice_date']);
$current_date = new DateTime();
$days_difference = $current_date->diff($invoice_date)->days;

if ($days_difference > 7 && !$bypass_return_window) {
    echo json_encode([
        'success' => false,
        'message' => 'This invoice is outside of the 7-day return window (' . $days_difference . ' days old)',
        'expired' => true,
        'days_old' => $days_difference,
        'invoice_number' => $invoice_number
    ]);
    exit;
}

// Check if invoice has already been fully returned - but we'll continue processing instead of exiting
$return_check_query = "SELECT COUNT(*) as count, SUM(return_amount) as total_returned 
                       FROM sales_returns 
                       WHERE invoice_id = '{$invoice['invoice_number']}'";
$return_check = fetch_data($return_check_query);

// Get invoice total from columns that might contain the total
$invoice_total = 0;
if (isset($invoice['total_amount'])) {
    $invoice_total = $invoice['total_amount'];
} elseif (isset($invoice['grand_total'])) {
    $invoice_total = $invoice['grand_total'];
} elseif (isset($invoice['invoice_total'])) {
    $invoice_total = $invoice['invoice_total'];
} elseif (isset($invoice['total'])) {
    $invoice_total = $invoice['total'];
}

// Check if fully returned but store for notification, don't exit
$is_fully_returned = false;
if (!empty($return_check) && isset($return_check[0]['total_returned']) && $return_check[0]['total_returned'] > 0 && $invoice_total > 0) {
    if ($return_check[0]['total_returned'] >= $invoice_total) {
        $is_fully_returned = true;
    }
}

// Get invoice items - query the sales table using proper column names from database structure
$items_data = [];
$invoice_id_field = isset($invoice['invoice_number']) ? $invoice['invoice_number'] : null;

// Try to get items from the sales table with the correct column names
$items_query = "SELECT s.*, p.product_name, pb.product_id, pb.batch_number, pb.selling_price, pb.cost, pb.batch_id, pb.discount_price , pb.batch_number AS batch_name
               FROM sales s
               LEFT JOIN products p ON s.product = p.product_name
               LEFT JOIN product_batch pb ON s.batch = pb.batch_id
               WHERE s.invoice_number = '$invoice_id_field'";
$items_data = fetch_data($items_query);

// If still empty, try alternative approaches
if (empty($items_data)) {
    // Try with just sales table if joins are the issue
    $alt_query = "SELECT sales.*, product_batch.batch_number AS batch_name
                FROM sales, product_batch
                WHERE invoice_number = '$invoice_id_field'
                    AND sales.batch = product_batch.batch_id";
    $items_data = fetch_data($alt_query);
}

$items = [];

// Get previously returned quantities for all items in this invoice
$returned_items_query = "SELECT 
                          sri.product_id, 
                          sri.batch_id,
                          SUM(sri.quantity_returned) as returned_qty
                      FROM sales_returns sr
                      JOIN sales_return_items sri ON sr.return_id = sri.return_id
                      WHERE sr.invoice_id = '{$invoice['invoice_number']}'
                      GROUP BY sri.product_id, sri.batch_id";
$returned_items = fetch_data($returned_items_query);

// Create a map for quick lookup of returned quantities by product_id and batch_id
$returned_items_map = [];
foreach ($returned_items as $item) {
    $key = $item['product_id'] . '_' . $item['batch_id'];
    $returned_items_map[$key] = $item['returned_qty'];
}

// Enhanced items with product and batch information
foreach ($items_data as $item) {
    // Get product ID from sales table based on database structure
    $product_id = null;
    $product_name = isset($item['product']) ? $item['product'] : null;
    
    // Get batch information
    $batch_number = isset($item['batch']) ? $item['batch'] : 'N/A';
    $batch_name = isset($item['batch_number']) ? $item['batch_number'] : 'N/A';
    $batch_id = isset($item['batch_id']) ? $item['batch_id'] : null;
    
    // Look up previously returned quantity for this item
    $returned_qty = 0;
    $lookup_key = $item['product_id'] . '_' . $batch_id;
    if (isset($returned_items_map[$lookup_key])) {
        $returned_qty = $returned_items_map[$lookup_key];
    }
    
    // Original quantity sold
    $original_qty = isset($item['qty']) ? $item['qty'] : 0;
    
    // Calculate remaining quantity that can be returned
    $remaining_qty = $original_qty - $returned_qty;
    
    // Include all items regardless of their returned status - will be handled in the frontend
    // Create item object with correct field mappings
    $enhanced_item = [
        'id' => isset($item['sales_id']) ? $item['sales_id'] : null,
        'item_id' => isset($item['sales_id']) ? $item['sales_id'] : null,
        'product_id' => isset($item['sales_id']) ? $item['sales_id'] : null, // Using sales_id as product identifier for returns
        'item_name' => $product_name,
        'batch_number' => $batch_name,
        'batch_id' => $batch_id,
        'qty' => $remaining_qty > 0 ? $remaining_qty : 0, // Use remaining quantity or 0 if fully returned
        'original_qty' => $original_qty,
        'returned_qty' => $returned_qty,
        'sale_price' => isset($item['rate']) ? $item['rate'] : 0,
        'cost' => isset($item['cost']) ? $item['cost'] : 0,
        'profit' => isset($item['profit']) ? $item['profit'] : 0,
        'discount_price' => isset($item['discount_price']) ? $item['discount_price'] : null
    ];
    
    $items[] = $enhanced_item;
}

// Format response - ensure we have fallbacks for the id field if it doesn't exist
$response = [
    'success' => true,
    'invoice' => [
        'invoice_number' => $invoice['invoice_number'],
        'invoice_date' => $invoice['invoice_date'],
        'customer_id' => $customer_id,
        'customer_name' => $customer_name,
        'items' => $items,
        'is_fully_returned' => $is_fully_returned
    ]
];

// Add id only if it exists, otherwise use invoice_number as the id
if (isset($invoice['id'])) {
    $response['invoice']['id'] = $invoice['id'];
} else {
    $response['invoice']['id'] = $invoice['invoice_number'];
}

// Add total amount, advance, and balance using the same approach - handle possible column names
if (isset($invoice['total_amount'])) {
    $response['invoice']['total_amount'] = $invoice['total_amount'];
} elseif (isset($invoice['grand_total'])) {
    $response['invoice']['total_amount'] = $invoice['grand_total'];
} elseif (isset($invoice['invoice_total'])) {
    $response['invoice']['total_amount'] = $invoice['invoice_total'];
} elseif (isset($invoice['total'])) {
    $response['invoice']['total_amount'] = $invoice['total'];
} else {
    $response['invoice']['total_amount'] = 0; // Default value
}

// Handle advance/payment
if (isset($invoice['advance'])) {
    $response['invoice']['advance'] = $invoice['advance'];
} elseif (isset($invoice['payment'])) {
    $response['invoice']['advance'] = $invoice['payment'];
} elseif (isset($invoice['paid_amount'])) {
    $response['invoice']['advance'] = $invoice['paid_amount'];
} else {
    $response['invoice']['advance'] = 0; // Default value
}

// Handle balance
if (isset($invoice['balance'])) {
    $response['invoice']['balance'] = $invoice['balance'];
} elseif (isset($invoice['due'])) {
    $response['invoice']['balance'] = $invoice['due'];
} elseif (isset($invoice['amount_due'])) {
    $response['invoice']['balance'] = $invoice['amount_due'];
} else {
    // Calculate balance if it's not explicitly stored
    $response['invoice']['balance'] = $response['invoice']['total_amount'] - $response['invoice']['advance'];
}

echo json_encode($response);
?>