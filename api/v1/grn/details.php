<?php
/**
 * GRN Details API
 * 
 * Get detailed information about a specific GRN
 * GET /api/v1/grn/details.php
 * 
 * Admin only
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate and require admin role
$user = ApiAuth::requireAuth();
if ($user['employee_role'] !== 'Admin') {
    ApiResponse::forbidden('Admin access required');
}

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get GRN ID from query parameter
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        ApiResponse::error('GRN ID is required', 400);
    }
    
    $grn_id = intval($_GET['id']);
    
    if ($grn_id <= 0) {
        ApiResponse::error('Valid GRN ID is required', 400);
    }
    
    // Query to get GRN header details
    $grn_query = "SELECT 
                    grn.*,
                    po.po_number,
                    po.po_id,
                    po.order_date,
                    s.supplier_name,
                    s.supplier_tel,
                    s.supplier_address,
                    e.emp_name as created_by_name,
                    e.employ_id as created_by_id
                  FROM goods_receipt_notes grn
                  LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
                  LEFT JOIN suppliers s ON grn.supplier_id = s.supplier_id
                  LEFT JOIN employees e ON grn.created_by = e.employ_id
                  WHERE grn.grn_id = $grn_id";
    
    $grn_result = mysqli_query($con, $grn_query);
    
    if (!$grn_result) {
        throw new Exception('Failed to fetch GRN details: ' . mysqli_error($con));
    }
    
    if (mysqli_num_rows($grn_result) === 0) {
        ApiResponse::error('GRN not found', 404);
    }
    
    $grn_data = mysqli_fetch_assoc($grn_result);
    
    // Query to get GRN items with product details
    $items_query = "SELECT 
                      gi.*,
                      p.product_name,
                      p.barcode,
                      p.sku,
                      p.description,
                      pb.batch_number,
                      pb.product_id,
                      (gi.received_qty * gi.cost) as total_cost,
                      (gi.received_qty * gi.selling_price) as total_selling_value
                    FROM grn_items gi
                    LEFT JOIN product_batch pb ON gi.batch_id = pb.batch_id
                    LEFT JOIN products p ON pb.product_id = p.product_id
                    WHERE gi.grn_id = $grn_id
                    ORDER BY gi.grn_item_id";
    
    $items_result = mysqli_query($con, $items_query);
    
    if (!$items_result) {
        throw new Exception('Failed to fetch GRN items: ' . mysqli_error($con));
    }
    
    // Collect items data
    $items = [];
    $total_items = 0;
    $total_quantity = 0;
    $calculated_total = 0;
    
    while ($item = mysqli_fetch_assoc($items_result)) {
        $items[] = [
            'grn_item_id' => (int)$item['grn_item_id'],
            'batch_id' => (int)$item['batch_id'],
            'batch_number' => $item['batch_number'],
            'product' => [
                'id' => (int)$item['product_id'],
                'name' => $item['product_name'],
                'sku' => $item['sku'],
                'barcode' => $item['barcode'],
                'description' => $item['description']
            ],
            'received_qty' => (float)$item['received_qty'],
            'cost' => (float)$item['cost'],
            'selling_price' => (float)$item['selling_price'],
            'expiry_date' => $item['expiry_date'],
            'notes' => $item['notes'],
            'total_cost' => (float)$item['total_cost'],
            'total_selling_value' => (float)$item['total_selling_value']
        ];
        
        $total_items++;
        $total_quantity += (float)$item['received_qty'];
        $calculated_total += (float)$item['total_cost'];
    }
    
    // Prepare response data
    $response_data = [
        'id' => (int)$grn_data['grn_id'],
        'grn_number' => $grn_data['grn_number'],
        'receipt_date' => $grn_data['receipt_date'],
        'invoice_number' => $grn_data['invoice_number'],
        'invoice_date' => $grn_data['invoice_date'],
        'status' => $grn_data['status'],
        'notes' => $grn_data['notes'],
        'purchase_order' => $grn_data['po_id'] ? [
            'id' => (int)$grn_data['po_id'],
            'po_number' => $grn_data['po_number'],
            'order_date' => $grn_data['order_date']
        ] : null,
        'supplier' => [
            'id' => (int)$grn_data['supplier_id'],
            'name' => $grn_data['supplier_name'],
            'mobile' => $grn_data['supplier_tel'],
            'address' => $grn_data['supplier_address']
        ],
        'payment' => [
            'total_amount' => (float)$grn_data['total_amount'],
            'paid_amount' => (float)$grn_data['paid_amount'],
            'outstanding_amount' => (float)$grn_data['outstanding_amount'],
            'payment_status' => $grn_data['payment_status'],
            'payment_method' => $grn_data['payment_method'],
            'payment_reference' => $grn_data['payment_reference'],
            'payment_notes' => $grn_data['payment_notes']
        ],
        'items' => $items,
        'summary' => [
            'item_count' => $total_items,
            'total_quantity' => $total_quantity,
            'calculated_total' => $calculated_total
        ],
        'created_by' => [
            'id' => (int)$grn_data['created_by_id'],
            'name' => $grn_data['created_by_name']
        ],
        'created_at' => $grn_data['created_at'],
        'updated_at' => $grn_data['updated_at']
    ];
    
    ApiResponse::success($response_data, 'GRN details retrieved successfully');
    
} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
