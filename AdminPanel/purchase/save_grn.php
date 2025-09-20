<?php
require_once '../../inc/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Handle both JSON and form POST data
    $input = null;
    $content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (stripos($content_type, 'application/json') !== false) {
        // Handle JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data');
        }
    } else {
        // Handle form POST data
        $input = $_POST;
        
        // Convert items from form format to expected array format
        if (isset($_POST['items']) && is_string($_POST['items'])) {
            $input['items'] = json_decode($_POST['items'], true);
        }
    }

    // Validate session
    if (!isset($_SESSION['employee_id']) || empty($_SESSION['employee_id'])) {
        throw new Exception('User session not found. Please login again.');
    }

    // Validate required fields
    $required_fields = ['supplier_id', 'receipt_date', 'items'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate items array
    if (!is_array($input['items']) || count($input['items']) === 0) {
        throw new Exception('At least one item is required for GRN');
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Generate GRN number
    $grn_number = generateGRNNumber($con);

    // Calculate total amount from items
    $total_amount = 0;
    foreach ($input['items'] as $item) {
        $total_amount += $item['cost'] * $item['received_qty'];
    }

    // Get payment data
    $payment_data = isset($input['payment_data']) ? $input['payment_data'] : [];
    $paid_amount = isset($payment_data['paid_amount']) ? $payment_data['paid_amount'] : 0;
    $payment_status = isset($payment_data['payment_status']) ? $payment_data['payment_status'] : 'unpaid';
    $payment_method = isset($payment_data['payment_method']) ? $payment_data['payment_method'] : null;
    $payment_reference = isset($payment_data['payment_reference']) ? $payment_data['payment_reference'] : null;
    $payment_notes = isset($payment_data['payment_notes']) ? $payment_data['payment_notes'] : null;

    // Validate payment data
    if ($payment_status === 'paid' && $paid_amount < $total_amount) {
        $payment_status = 'partial';
    } elseif ($payment_status === 'partial' && $paid_amount >= $total_amount) {
        $payment_status = 'paid';
    } elseif ($paid_amount <= 0) {
        $payment_status = 'unpaid';
        $paid_amount = 0;
    }

    // Insert GRN header with payment information
    $grn_sql = "
        INSERT INTO goods_receipt_notes (
            grn_number, po_id, supplier_id, receipt_date, invoice_number,
            invoice_date, notes, status, total_amount, paid_amount, 
            payment_status, payment_method, payment_reference, payment_notes,
            created_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ";

    $grn_status = 'completed'; // Default status for GRN
    $po_id = isset($input['po_id']) ? $input['po_id'] : null;
    
    $stmt = mysqli_prepare($con, $grn_sql);
    mysqli_stmt_bind_param(
        $stmt,
        'siisssssddssssi',
        $grn_number,
        $po_id,
        $input['supplier_id'],
        $input['receipt_date'],
        $input['invoice_number'],
        $input['invoice_date'],
        $input['notes'],
        $grn_status,
        $total_amount,
        $paid_amount,
        $payment_status,
        $payment_method,
        $payment_reference,
        $payment_notes,
        $_SESSION['employee_id']
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to create GRN: ' . mysqli_error($con));
    }

    $grn_id = mysqli_insert_id($con);

    // Process items with enhanced batch management
    foreach ($input['items'] as $item) {
        // Check if product exists
        $product_check_sql = "SELECT COUNT(*) as count FROM products WHERE product_id = ?";
        $product_check_stmt = mysqli_prepare($con, $product_check_sql);
        mysqli_stmt_bind_param($product_check_stmt, 'i', $item['product_id']);
        mysqli_stmt_execute($product_check_stmt);
        $product_result = mysqli_stmt_get_result($product_check_stmt);
        $product_exists = mysqli_fetch_assoc($product_result)['count'] > 0;
        
        if (!$product_exists) {
            throw new Exception("Product ID {$item['product_id']} does not exist");
        }
        
        $batch_id = null;
        $batchData = isset($item['batch_data']) ? $item['batch_data'] : null;
        
        if ($batchData && isset($batchData['isNew'])) {
            if ($batchData['isNew']) {
                // Create new batch
                $batch_number = !empty($batchData['batchNumber']) ? $batchData['batchNumber'] : 'B' . date('Ymd') . '-' . $item['product_id'];
                $alert_quantity = isset($batchData['alertQuantity']) ? $batchData['alertQuantity'] : 5;
                $batch_status = isset($batchData['status']) ? $batchData['status'] : 'active';
                $discount_price = isset($batchData['discountPrice']) && $batchData['discountPrice'] > 0 ? $batchData['discountPrice'] : null;
                
                $batch_sql = "
                    INSERT INTO product_batch (
                        product_id, batch_number, cost, selling_price,
                        expiry_date, quantity, alert_quantity, supplier_id, 
                        status, discount_price, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ";
                
                $batch_stmt = mysqli_prepare($con, $batch_sql);
                mysqli_stmt_bind_param(
                    $batch_stmt,
                    'isddsddisd',
                    $item['product_id'],
                    $batch_number,
                    $item['cost'],
                    $item['selling_price'],
                    $item['expiry_date'],
                    $item['received_qty'],
                    $alert_quantity,
                    $input['supplier_id'],
                    $batch_status,
                    $discount_price
                );
                
                if (!mysqli_stmt_execute($batch_stmt)) {
                    throw new Exception('Failed to create new batch: ' . mysqli_error($con));
                }
                
                $batch_id = mysqli_insert_id($con);
            } else {
                // Use existing batch - update quantity
                $existing_batch_id = $batchData['batchId'];
                
                // Verify batch exists and get current quantity
                $check_batch_sql = "SELECT quantity FROM product_batch WHERE batch_id = ? AND product_id = ?";
                $check_batch_stmt = mysqli_prepare($con, $check_batch_sql);
                mysqli_stmt_bind_param($check_batch_stmt, 'ii', $existing_batch_id, $item['product_id']);
                mysqli_stmt_execute($check_batch_stmt);
                $batch_result = mysqli_stmt_get_result($check_batch_stmt);
                
                if (mysqli_num_rows($batch_result) === 0) {
                    throw new Exception("Selected batch does not exist for product ID {$item['product_id']}");
                }
                
                $current_batch = mysqli_fetch_assoc($batch_result);
                $new_quantity = $current_batch['quantity'] + $item['received_qty'];
                
                // Update existing batch
                $update_batch_sql = "
                    UPDATE product_batch 
                    SET quantity = ?,
                        cost = ?,
                        selling_price = ?,
                        expiry_date = COALESCE(?, expiry_date),
                        restocked_at = NOW()
                    WHERE batch_id = ?
                ";
                
                $update_batch_stmt = mysqli_prepare($con, $update_batch_sql);
                mysqli_stmt_bind_param(
                    $update_batch_stmt,
                    'dddsi',
                    $new_quantity,
                    $item['cost'],
                    $item['selling_price'],
                    $item['expiry_date'],
                    $existing_batch_id
                );
                
                if (!mysqli_stmt_execute($update_batch_stmt)) {
                    throw new Exception('Failed to update existing batch: ' . mysqli_error($con));
                }
                
                $batch_id = $existing_batch_id;
            }
        } else {
            // Fallback to old logic for backward compatibility
            $batch_number = !empty($item['batch_number']) ? $item['batch_number'] : 'B' . date('Ymd');
            
            // Check if batch exists
            $check_batch_sql = "
                SELECT batch_id, quantity 
                FROM product_batch 
                WHERE product_id = ? AND batch_number = ?
            ";
            $check_stmt = mysqli_prepare($con, $check_batch_sql);
            mysqli_stmt_bind_param($check_stmt, 'is', $item['product_id'], $batch_number);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                // Batch exists, update it
                $batch_data = mysqli_fetch_assoc($check_result);
                $batch_id = $batch_data['batch_id'];
                $current_qty = $batch_data['quantity'];
                $new_qty = $current_qty + $item['received_qty'];
                
                $batch_sql = "
                    UPDATE product_batch 
                    SET quantity = ?,
                        cost = ?,
                        selling_price = ?,
                        expiry_date = ?,
                        restocked_at = NOW()
                    WHERE batch_id = ?
                ";
                
                $batch_stmt = mysqli_prepare($con, $batch_sql);
                mysqli_stmt_bind_param(
                    $batch_stmt,
                    'dddsi',
                    $new_qty,
                    $item['cost'],
                    $item['selling_price'],
                    $item['expiry_date'],
                    $batch_id
                );
                
                if (!mysqli_stmt_execute($batch_stmt)) {
                    throw new Exception('Failed to update batch: ' . mysqli_error($con));
                }
            } else {
                // Create new batch
                $batch_sql = "
                    INSERT INTO product_batch (
                        product_id, batch_number, cost, selling_price,
                        expiry_date, quantity, supplier_id, status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
                ";
                
                $batch_stmt = mysqli_prepare($con, $batch_sql);
                mysqli_stmt_bind_param(
                    $batch_stmt,
                    'isddsdi',
                    $item['product_id'],
                    $batch_number,
                    $item['cost'],
                    $item['selling_price'],
                    $item['expiry_date'],
                    $item['received_qty'],
                    $input['supplier_id']
                );
                
                if (!mysqli_stmt_execute($batch_stmt)) {
                    throw new Exception('Failed to create batch: ' . mysqli_error($con));
                }
                
                $batch_id = mysqli_insert_id($con);
            }
        }

        // Insert GRN item
        $po_item_id = isset($item['po_item_id']) ? $item['po_item_id'] : null;
        
        $grn_item_sql = "
            INSERT INTO grn_items (
                grn_id, po_item_id, batch_id, received_qty,
                cost, selling_price, expiry_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        
        $grn_item_stmt = mysqli_prepare($con, $grn_item_sql);
        mysqli_stmt_bind_param(
            $grn_item_stmt,
            'iiiddds',
            $grn_id,
            $po_item_id,
            $batch_id,
            $item['received_qty'],
            $item['cost'],
            $item['selling_price'],
            $item['expiry_date']
        );
        
        if (!mysqli_stmt_execute($grn_item_stmt)) {
            throw new Exception('Failed to add GRN item: ' . mysqli_error($con));
        }
        
        // Stock is automatically calculated from product_batch table via product_view
        // No manual stock update needed
        
        // If from PO, update PO item received quantity
        if ($po_id && $po_item_id) {
            $update_po_item_sql = "
                UPDATE purchase_order_items 
                SET received_qty = received_qty + ?,
                    status = CASE 
                        WHEN received_qty + ? >= quantity THEN 'completed' 
                        ELSE 'partial' 
                    END
                WHERE po_item_id = ?
            ";
            
            $update_po_stmt = mysqli_prepare($con, $update_po_item_sql);
            mysqli_stmt_bind_param(
                $update_po_stmt,
                'ddi',
                $item['received_qty'],
                $item['received_qty'],
                $po_item_id
            );
            
            if (!mysqli_stmt_execute($update_po_stmt)) {
                throw new Exception('Failed to update PO item: ' . mysqli_error($con));
            }
        }
    }
    
    // If from PO, check if all items are received and update PO status
    if ($po_id) {
        $check_po_sql = "
            SELECT 
                (SELECT COUNT(*) FROM purchase_order_items WHERE po_id = ?) as total_items,
                (SELECT COUNT(*) FROM purchase_order_items WHERE po_id = ? AND status = 'completed') as completed_items
        ";
        
        $check_po_stmt = mysqli_prepare($con, $check_po_sql);
        mysqli_stmt_bind_param($check_po_stmt, 'ii', $po_id, $po_id);
        mysqli_stmt_execute($check_po_stmt);
        $po_status_result = mysqli_stmt_get_result($check_po_stmt);
        $po_status_data = mysqli_fetch_assoc($po_status_result);
        
        if ($po_status_data['total_items'] > 0 && $po_status_data['total_items'] == $po_status_data['completed_items']) {
            // All items received, mark PO as completed
            $update_po_sql = "UPDATE purchase_orders SET status = 'completed' WHERE po_id = ?";
            $update_po_stmt = mysqli_prepare($con, $update_po_sql);
            mysqli_stmt_bind_param($update_po_stmt, 'i', $po_id);
            mysqli_stmt_execute($update_po_stmt);
        } else if ($po_status_data['completed_items'] > 0) {
            // Some items received, mark PO as partial
            $update_po_sql = "UPDATE purchase_orders SET status = 'partial' WHERE po_id = ?";
            $update_po_stmt = mysqli_prepare($con, $update_po_sql);
            mysqli_stmt_bind_param($update_po_stmt, 'i', $po_id);
            mysqli_stmt_execute($update_po_stmt);
        }
    }
    
    // Process Payment and Update Supplier Balance
    $outstanding_amount = $total_amount - $paid_amount;
    
    if ($paid_amount > 0) {
        // Record payment in supplier_payments table
        $payment_sql = "
            INSERT INTO supplier_payments (
                supplier_id, date, amount, method, reference_no, note, 
                created_by, grn_id, po_id, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ";
        
        $payment_stmt = mysqli_prepare($con, $payment_sql);
        $payment_date = date('Y-m-d'); // Use current date for payment
        $payment_note = !empty($payment_notes) ? $payment_notes : "Payment for GRN #$grn_number";
        
        mysqli_stmt_bind_param(
            $payment_stmt,
            'isdsssiii',
            $input['supplier_id'],
            $payment_date,
            $paid_amount,
            $payment_method,
            $payment_reference,
            $payment_note,
            $_SESSION['employee_id'],
            $grn_id,
            $po_id
        );
        
        if (!mysqli_stmt_execute($payment_stmt)) {
            throw new Exception('Failed to record payment: ' . mysqli_error($con));
        }
    }
    
    // Update supplier credit balance for outstanding amount
    if ($outstanding_amount > 0) {
        $update_supplier_sql = "
            UPDATE suppliers 
            SET credit_balance = credit_balance + ? 
            WHERE supplier_id = ?
        ";
        
        $update_supplier_stmt = mysqli_prepare($con, $update_supplier_sql);
        mysqli_stmt_bind_param($update_supplier_stmt, 'di', $outstanding_amount, $input['supplier_id']);
        
        if (!mysqli_stmt_execute($update_supplier_stmt)) {
            throw new Exception('Failed to update supplier balance: ' . mysqli_error($con));
        }
    }
    
    // Enhanced Transaction Logging
    $transaction_sql = "
        INSERT INTO transaction_log (
            transaction_type, description, amount, employ_id, transaction_date, transaction_time
        ) VALUES (?, ?, ?, ?, CURDATE(), CURTIME())
    ";
    
    // 1. Log GRN Creation
    $grn_type = 'GRN_CREATED';
    $grn_description = "Created GRN #$grn_number - Total Amount: Rs.{$total_amount}";
    $grn_amount = $total_amount;
    $employee_id = $_SESSION['employee_id'];
    
    $transaction_stmt = mysqli_prepare($con, $transaction_sql);
    mysqli_stmt_bind_param(
        $transaction_stmt,
        'ssii',
        $grn_type,
        $grn_description,
        $grn_amount,
        $employee_id
    );
    mysqli_stmt_execute($transaction_stmt);
    
    // 2. Log Payment Transaction (if payment made)
    if ($paid_amount > 0) {
        $payment_type = 'SUPPLIER_PAYMENT';
        $payment_description = "Payment for GRN #$grn_number - Method: " . ($payment_method ?: 'Not specified') . 
                              ", Reference: " . ($payment_reference ?: 'N/A') . 
                              ", Status: " . ucfirst($payment_status);
        $payment_amount = $paid_amount;
        
        $payment_transaction_stmt = mysqli_prepare($con, $transaction_sql);
        mysqli_stmt_bind_param(
            $payment_transaction_stmt,
            'ssii',
            $payment_type,
            $payment_description,
            $payment_amount,
            $employee_id
        );
        mysqli_stmt_execute($payment_transaction_stmt);
    }
    
    // 3. Log Credit Balance Update (if outstanding amount)
    if ($outstanding_amount > 0) {
        $credit_type = 'SUPPLIER_CREDIT';
        $credit_description = "Credit balance increased for GRN #$grn_number - Outstanding: Rs.{$outstanding_amount}";
        $credit_amount = $outstanding_amount;
        
        $credit_transaction_stmt = mysqli_prepare($con, $transaction_sql);
        mysqli_stmt_bind_param(
            $credit_transaction_stmt,
            'ssii',
            $credit_type,
            $credit_description,
            $credit_amount,
            $employee_id
        );
        mysqli_stmt_execute($credit_transaction_stmt);
    }

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'GRN created successfully',
        'grn_id' => $grn_id,
        'grn_number' => $grn_number,
        'payment_info' => [
            'total_amount' => $total_amount,
            'paid_amount' => $paid_amount,
            'outstanding_amount' => $outstanding_amount,
            'payment_status' => $payment_status
        ]
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

/**
 * Generate a unique GRN number
 */
function generateGRNNumber($con) {
    // Get next sequence value
    $seq_sql = "
        INSERT INTO sequences (name, prefix, next_value, padding)
        VALUES ('grn_number', 'GRN', 1, 5)
        ON DUPLICATE KEY UPDATE next_value = next_value + 1
    ";

    mysqli_query($con, $seq_sql);
    if (mysqli_error($con)) {
        throw new Exception('Failed to generate GRN number: ' . mysqli_error($con));
    }

    $get_seq_sql = "SELECT next_value - 1 as value, prefix, padding 
                    FROM sequences WHERE name = 'grn_number'";
    $seq_result = mysqli_query($con, $get_seq_sql);
    $seq_data = mysqli_fetch_assoc($seq_result);

    return $seq_data['prefix'] . str_pad(
        $seq_data['value'],
        $seq_data['padding'],
        '0',
        STR_PAD_LEFT
    );
}
        