<?php
/**
 * Create GRN API
 * 
 * Create a new Goods Received Note with items
 * POST /api/v1/grn/create.php
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

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

/**
 * Generate unique GRN number
 */
function generateGRNNumber($con) {
    $prefix = 'GRN';
    $date_part = date('Ymd');
    
    // Get the last GRN number for today
    $query = "SELECT grn_number FROM goods_receipt_notes 
              WHERE grn_number LIKE '$prefix-$date_part-%' 
              ORDER BY grn_id DESC LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_number = $row['grn_number'];
        $parts = explode('-', $last_number);
        $sequence = isset($parts[2]) ? intval($parts[2]) + 1 : 1;
    } else {
        $sequence = 1;
    }
    
    return sprintf('%s-%s-%04d', $prefix, $date_part, $sequence);
}

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        ApiResponse::error('Invalid JSON data', 400);
    }
    
    // Validate required fields
    $required_fields = ['supplier_id', 'receipt_date', 'items'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        ApiResponse::error('Missing required fields: ' . implode(', ', $missing_fields), 400);
    }
    
    // Validate supplier exists
    $supplier_id = intval($input['supplier_id']);
    $supplier_check = mysqli_query($con, "SELECT supplier_id FROM suppliers WHERE supplier_id = $supplier_id");
    if (!$supplier_check || mysqli_num_rows($supplier_check) === 0) {
        ApiResponse::error('Invalid supplier ID', 400);
    }
    
    // Validate items array
    if (!is_array($input['items']) || count($input['items']) === 0) {
        ApiResponse::error('At least one item is required for GRN', 400);
    }
    
    // Validate each item
    foreach ($input['items'] as $index => $item) {
        $item_required = ['product_id', 'received_qty', 'cost', 'selling_price'];
        foreach ($item_required as $field) {
            if (!isset($item[$field]) || $item[$field] === '') {
                ApiResponse::error("Item #" . ($index + 1) . ": $field is required", 400);
            }
        }
        
        if ($item['received_qty'] <= 0) {
            ApiResponse::error("Item #" . ($index + 1) . ": Received quantity must be greater than 0", 400);
        }
        
        if ($item['cost'] < 0 || $item['selling_price'] < 0) {
            ApiResponse::error("Item #" . ($index + 1) . ": Prices cannot be negative", 400);
        }
    }
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        // Generate GRN number
        $grn_number = generateGRNNumber($con);
        
        // Calculate total amount from items
        $total_amount = 0;
        foreach ($input['items'] as $item) {
            $total_amount += $item['cost'] * $item['received_qty'];
        }
        
        // Get optional fields
        $po_id = isset($input['po_id']) && $input['po_id'] > 0 ? intval($input['po_id']) : null;
        $receipt_date = mysqli_real_escape_string($con, $input['receipt_date']);
        $invoice_number = isset($input['invoice_number']) ? mysqli_real_escape_string($con, $input['invoice_number']) : null;
        $invoice_date = isset($input['invoice_date']) ? mysqli_real_escape_string($con, $input['invoice_date']) : null;
        $notes = isset($input['notes']) ? mysqli_real_escape_string($con, $input['notes']) : null;
        $status = isset($input['status']) && in_array($input['status'], ['draft', 'completed', 'cancelled']) ? $input['status'] : 'completed';
        
        // Payment data
        $paid_amount = isset($input['paid_amount']) ? floatval($input['paid_amount']) : 0;
        $payment_method = isset($input['payment_method']) ? mysqli_real_escape_string($con, $input['payment_method']) : null;
        $payment_reference = isset($input['payment_reference']) ? mysqli_real_escape_string($con, $input['payment_reference']) : null;
        $payment_notes = isset($input['payment_notes']) ? mysqli_real_escape_string($con, $input['payment_notes']) : null;
        
        // Determine payment status
        if ($paid_amount >= $total_amount) {
            $payment_status = 'paid';
            $paid_amount = $total_amount;
        } elseif ($paid_amount > 0) {
            $payment_status = 'partial';
        } else {
            $payment_status = 'unpaid';
            $paid_amount = 0;
        }
        
        $created_by = $user['employee_id'];
        
        // Insert GRN header
        $grn_sql = "INSERT INTO goods_receipt_notes (
                        grn_number, po_id, supplier_id, receipt_date, invoice_number,
                        invoice_date, notes, status, total_amount, paid_amount,
                        payment_status, payment_method, payment_reference, payment_notes,
                        created_by, created_at
                    ) VALUES (
                        '$grn_number', " . ($po_id ? $po_id : 'NULL') . ", $supplier_id, '$receipt_date',
                        " . ($invoice_number ? "'$invoice_number'" : 'NULL') . ",
                        " . ($invoice_date ? "'$invoice_date'" : 'NULL') . ",
                        " . ($notes ? "'$notes'" : 'NULL') . ",
                        '$status', $total_amount, $paid_amount, '$payment_status',
                        " . ($payment_method ? "'$payment_method'" : 'NULL') . ",
                        " . ($payment_reference ? "'$payment_reference'" : 'NULL') . ",
                        " . ($payment_notes ? "'$payment_notes'" : 'NULL') . ",
                        $created_by, NOW()
                    )";
        
        if (!mysqli_query($con, $grn_sql)) {
            throw new Exception('Failed to create GRN: ' . mysqli_error($con));
        }
        
        $grn_id = mysqli_insert_id($con);
        
        // Process items
        foreach ($input['items'] as $item) {
            $product_id = intval($item['product_id']);
            $received_qty = floatval($item['received_qty']);
            $cost = floatval($item['cost']);
            $selling_price = floatval($item['selling_price']);
            $expiry_date = isset($item['expiry_date']) && !empty($item['expiry_date']) ? 
                           "'" . mysqli_real_escape_string($con, $item['expiry_date']) . "'" : 'NULL';
            $item_notes = isset($item['notes']) ? "'" . mysqli_real_escape_string($con, $item['notes']) . "'" : 'NULL';
            $batch_number = isset($item['batch_number']) && !empty($item['batch_number']) ? 
                           mysqli_real_escape_string($con, $item['batch_number']) : 
                           'B' . date('Ymd') . '-' . $product_id;
            
            // Verify product exists
            $product_check = mysqli_query($con, "SELECT product_id FROM products WHERE product_id = $product_id");
            if (!$product_check || mysqli_num_rows($product_check) === 0) {
                throw new Exception("Product ID $product_id does not exist");
            }
            
            // Check if batch exists
            $batch_check_sql = "SELECT batch_id, quantity FROM product_batch 
                               WHERE product_id = $product_id AND batch_number = '$batch_number'";
            $batch_check = mysqli_query($con, $batch_check_sql);
            
            if ($batch_check && mysqli_num_rows($batch_check) > 0) {
                // Update existing batch
                $batch_data = mysqli_fetch_assoc($batch_check);
                $batch_id = $batch_data['batch_id'];
                $new_qty = $batch_data['quantity'] + $received_qty;
                
                $update_batch_sql = "UPDATE product_batch 
                                    SET quantity = $new_qty,
                                        cost = $cost,
                                        selling_price = $selling_price,
                                        expiry_date = COALESCE($expiry_date, expiry_date),
                                        restocked_at = NOW()
                                    WHERE batch_id = $batch_id";
                
                if (!mysqli_query($con, $update_batch_sql)) {
                    throw new Exception('Failed to update batch: ' . mysqli_error($con));
                }
            } else {
                // Create new batch
                $alert_quantity = isset($item['alert_quantity']) ? floatval($item['alert_quantity']) : 5;
                
                $create_batch_sql = "INSERT INTO product_batch (
                                        product_id, batch_number, cost, selling_price,
                                        expiry_date, quantity, alert_quantity, supplier_id,
                                        status, created_at
                                    ) VALUES (
                                        $product_id, '$batch_number', $cost, $selling_price,
                                        $expiry_date, $received_qty, $alert_quantity, $supplier_id,
                                        'active', NOW()
                                    )";
                
                if (!mysqli_query($con, $create_batch_sql)) {
                    throw new Exception('Failed to create batch: ' . mysqli_error($con));
                }
                
                $batch_id = mysqli_insert_id($con);
            }
            
            // Insert GRN item
            $po_item_id = isset($item['po_item_id']) && $item['po_item_id'] > 0 ? intval($item['po_item_id']) : 'NULL';
            
            $grn_item_sql = "INSERT INTO grn_items (
                                grn_id, po_item_id, batch_id, received_qty,
                                cost, selling_price, expiry_date, notes
                            ) VALUES (
                                $grn_id, $po_item_id, $batch_id, $received_qty,
                                $cost, $selling_price, $expiry_date, $item_notes
                            )";
            
            if (!mysqli_query($con, $grn_item_sql)) {
                throw new Exception('Failed to create GRN item: ' . mysqli_error($con));
            }
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        // Fetch the created GRN details
        $grn_details_query = "SELECT 
                                grn.*,
                                s.supplier_name,
                                s.supplier_tel,
                                e.emp_name as created_by_name
                              FROM goods_receipt_notes grn
                              LEFT JOIN suppliers s ON grn.supplier_id = s.supplier_id
                              LEFT JOIN employees e ON grn.created_by = e.employ_id
                              WHERE grn.grn_id = $grn_id";
        
        $grn_details_result = mysqli_query($con, $grn_details_query);
        $grn_details = mysqli_fetch_assoc($grn_details_result);
        
        $response_data = [
            'id' => (int)$grn_details['grn_id'],
            'grn_number' => $grn_details['grn_number'],
            'receipt_date' => $grn_details['receipt_date'],
            'invoice_number' => $grn_details['invoice_number'],
            'status' => $grn_details['status'],
            'total_amount' => (float)$grn_details['total_amount'],
            'paid_amount' => (float)$grn_details['paid_amount'],
            'payment_status' => $grn_details['payment_status'],
            'supplier' => [
                'id' => (int)$grn_details['supplier_id'],
                'name' => $grn_details['supplier_name'],
                'mobile' => $grn_details['supplier_tel']
            ],
            'created_by' => $grn_details['created_by_name'],
            'created_at' => $grn_details['created_at']
        ];
        
        ApiResponse::success($response_data, 'GRN created successfully', 201);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        throw $e;
    }
    
} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
