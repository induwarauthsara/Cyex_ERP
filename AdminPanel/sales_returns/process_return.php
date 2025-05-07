<?php
require_once('../../inc/config.php');
header('Content-Type: application/json');

// Get input from POST request
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request format',
        'error_details' => 'Could not parse JSON input'
    ]);
    exit;
}

// For debugging
error_log("Return request: " . json_encode($input));

// Start a transaction
mysqli_begin_transaction($con);

try {
    // Get invoice details
    $invoice_number = $input['invoice_number'];
    $invoice_query = "SELECT * FROM invoice WHERE invoice_number = '$invoice_number'";
    $invoice_data = fetch_data($invoice_query);
    
    if (empty($invoice_data)) {
        throw new Exception('Invoice not found');
    }
    
    $invoice = $invoice_data[0];
    $customer_id = null;
    
    // Get customer ID if available
    if (!empty($invoice['customer_mobile'])) {
        $customer_query = "SELECT id FROM customers WHERE customer_mobile = '{$invoice['customer_mobile']}'";
        $customer_data = fetch_data($customer_query);
        if (!empty($customer_data)) {
            $customer_id = $customer_data[0]['id'];
        }
    }
    
    // Calculate total return amount
    $total_return_amount = 0;
    foreach ($input['items'] as $item) {
        $total_return_amount += $item['price'] * $item['quantity'];
    }
    
    // Get employee ID
    $employee_id = $_SESSION['employee_id'] ?? 0;
    
    // Format reason
    $return_reason = mysqli_real_escape_string($con, $input['reason']);
    $refund_method = mysqli_real_escape_string($con, $input['refund_method']);
    $return_note = mysqli_real_escape_string($con, $input['note'] ?? '');
    
    // Create return record - USING DIRECT MYSQLI QUERY FOR PROPER ID RETRIEVAL
    $return_query = "INSERT INTO sales_returns (
        invoice_id, 
        customer_id, 
        return_amount, 
        refund_method, 
        return_reason, 
        return_note, 
        user_id
    ) VALUES (
        '$invoice_number', 
        " . ($customer_id ? "'$customer_id'" : "NULL") . ", 
        '$total_return_amount', 
        '$refund_method', 
        '$return_reason', 
        '$return_note', 
        '$employee_id'
    )";
    
    error_log("Return query: $return_query");
    
    // Execute the query directly for better control
    $return_result = mysqli_query($con, $return_query);
    
    if (!$return_result) {
        throw new Exception('Failed to create return record: ' . mysqli_error($con));
    }
    
    // Get the inserted return ID
    $return_id = mysqli_insert_id($con);
    
    if (!$return_id) {
        throw new Exception('Failed to get return ID after insertion');
    }
    
    error_log("Created return record with ID: $return_id");
    
    // Log the action separately
    if (isset($_SESSION['employee_id'])) {
        $emp_id = $_SESSION['employee_id'];
        $log_query = "INSERT INTO action_log (employee_id, action, description) VALUES ('$emp_id', 'Sales Return', 'Created sales return for invoice #$invoice_number')";
        mysqli_query($con, $log_query);
    } else {
        $log_query = "INSERT INTO action_log (action, description) VALUES ('Sales Return', 'Created sales return for invoice #$invoice_number')";
        mysqli_query($con, $log_query);
    }
    
    // Process individual return items
    foreach ($input['items'] as $item) {
        $item_id = $item['item_id'];
        $batch_id = $item['batch_id'] ?? null;
        $quantity = floatval($item['quantity']);
        $price = floatval($item['price']);
        $add_to_stock = $input['add_to_stock'];
        
        // Get product information from sales record
        $sales_query = "SELECT * FROM sales WHERE sales_id = '$item_id'";
        $sales_data = fetch_data($sales_query);
        
        if (empty($sales_data)) {
            throw new Exception("Sales item not found with ID: $item_id");
        }
        
        $product_name = $sales_data[0]['product'];
        $batch_number = $sales_data[0]['batch'] ?? null;
        
        // If batch_id is not provided but batch_number exists in sales, try to get batch_id
        if (!$batch_id && $batch_number) {
            $batch_query = "SELECT batch_id FROM product_batch WHERE batch_number = '$batch_number'";
            $batch_data = fetch_data($batch_query);
            if (!empty($batch_data)) {
                $batch_id = $batch_data[0]['batch_id'];
            }
        }
        
        // Get product ID from product name
        $product_query = "SELECT product_id FROM products WHERE product_name = '$product_name'";
        $product_data = fetch_data($product_query);
        
        $product_id = !empty($product_data) ? $product_data[0]['product_id'] : null;
        
        // If product ID is still null, use sales_id as a fallback
        if ($product_id === null) {
            $product_id = $item_id;
            error_log("Using sales_id $item_id as fallback for product_id");
        }
        
        // Find a valid batch_id if it's still null but required
        if ($batch_id === null) {
            // Try to find any batch for this product
            $find_batch_query = "SELECT batch_id FROM product_batch WHERE product_id = '$product_id' ORDER BY created_at DESC LIMIT 1";
            $batch_result = fetch_data($find_batch_query);
            
            if (!empty($batch_result)) {
                $batch_id = $batch_result[0]['batch_id'];
                error_log("Found alternative batch_id: $batch_id for product_id: $product_id");
            } else {
                // Create a new dummy batch record if none exists
                $insert_batch_query = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, quantity, created_at) 
                                      VALUES ('$product_id', 'RET-".time()."', '$price', '$price', 0, NOW())";
                
                if (mysqli_query($con, $insert_batch_query)) {
                    $batch_id = mysqli_insert_id($con);
                    error_log("Created new batch_id: $batch_id for return");
                } else {
                    error_log("Failed to create batch: " . mysqli_error($con));
                    throw new Exception("Failed to create batch: " . mysqli_error($con));
                }
            }
        }
        
        // We must have a valid batch_id at this point
        if (!$batch_id) {
            throw new Exception("Could not find or create a valid batch_id for product: $product_name");
        }
        
        // Insert return item record
        $return_item_query = "INSERT INTO sales_return_items (
            return_id, 
            product_id, 
            batch_id, 
            quantity_returned, 
            return_price, 
            add_to_stock
        ) VALUES (
            '$return_id', 
            '$product_id', 
            '$batch_id', 
            '$quantity', 
            '$price', 
            '$add_to_stock'
        )";
        
        error_log("Return item query: $return_item_query");
        $return_item_result = mysqli_query($con, $return_item_query);
        
        if (!$return_item_result) {
            $sql_error = mysqli_error($con);
            $sql_errno = mysqli_errno($con);
            throw new Exception("Failed to add return item. Error #$sql_errno: $sql_error");
        }
        
        // Log action after successful query
        if (isset($_SESSION['employee_id'])) {
            $employee_id = $_SESSION['employee_id'];
            $action_log_query = "INSERT INTO action_log (employee_id, action, description) VALUES ('$employee_id', 'Return Item', 'Added return item: Product #$product_name, Qty: $quantity')";
        } else {
            $action_log_query = "INSERT INTO action_log (action, description) VALUES ('Return Item', 'Added return item: Product #$product_name, Qty: $quantity')";
        }
        mysqli_query($con, $action_log_query);
        
        // Update stock if needed and if we have a valid batch_id
        if ($add_to_stock == 1 && $batch_id) {
            // Get current stock
            $stock_query = "SELECT quantity FROM product_batch WHERE batch_id = '$batch_id'";
            $stock_data = fetch_data($stock_query);
            
            if (!empty($stock_data)) {
                $current_stock = floatval($stock_data[0]['quantity']);
                $new_stock = $current_stock + $quantity;
                
                // Update stock
                $update_stock_query = "UPDATE product_batch SET quantity = '$new_stock' WHERE batch_id = '$batch_id'";
                $update_stock_result = mysqli_query($con, $update_stock_query);
                
                if (!$update_stock_result) {
                    throw new Exception('Failed to update stock: ' . mysqli_error($con));
                }
                
                // Log stock update action
                if (isset($_SESSION['employee_id'])) {
                    $employee_id = $_SESSION['employee_id'];
                    $action_log_query = "INSERT INTO action_log (employee_id, action, description) VALUES ('$employee_id', 'Update Stock', 'Updated stock for batch #$batch_id: +$quantity')";
                } else {
                    $action_log_query = "INSERT INTO action_log (action, description) VALUES ('Update Stock', 'Updated stock for batch #$batch_id: +$quantity')";
                }
                mysqli_query($con, $action_log_query);
            }
        }
    }
    
    // Handle refund method
    if ($refund_method === 'Store Credit' && $customer_id) {
        // Add to customer's store credit
        // $customer_query = "SELECT customer_extra_fund FROM customers WHERE id = '$customer_id'";
        // $customer_data = fetch_data($customer_query);
        
        // if (!empty($customer_data)) {
        //     // $current_credit = floatval($customer_data[0]['customer_extra_fund'] ?? 0);
        //     // $new_credit = $current_credit + $total_return_amount;
            
        //     // Update customer credit
        //     // $update_credit_query = "UPDATE customers SET customer_extra_fund = '$new_credit' WHERE id = '$customer_id'";
        //     // $update_credit_result = mysqli_query($con, $update_credit_query);
            
        //     // if (!$update_credit_result) {
        //     //     throw new Exception('Failed to update customer store credit: ' . mysqli_error($con));
        //     // }
            
        //     // Log credit update action
        //     if (isset($_SESSION['employee_id'])) {
        //         $employee_id = $_SESSION['employee_id'];
        //         $action_log_query = "INSERT INTO action_log (employee_id, action, description) VALUES ('$employee_id', 'Store Credit', 'Added store credit to customer #$customer_id: +$total_return_amount')";
        //     } else {
        //         $action_log_query = "INSERT INTO action_log (action, description) VALUES ('Store Credit', 'Added store credit to customer #$customer_id: +$total_return_amount')";
        //     }
        //     mysqli_query($con, $action_log_query);
            
        //     // Add fund transaction record if the table exists
        //     try {
        //         $fund_transaction_query = "INSERT INTO fund_transactions (
        //             customer_id, 
        //             amount, 
        //             type, 
        //             description, 
        //             invoice_id
        //         ) VALUES (
        //             '$customer_id',
        //             '$total_return_amount',
        //             'addition',
        //             'Return credit from invoice #$invoice_number',
        //             '$invoice_number'
        //         )";
        //         $fund_transaction_result = mysqli_query($con, $fund_transaction_query);
                
        //         if (!$fund_transaction_result) {
        //             error_log("Warning: Could not add fund transaction record: " . mysqli_error($con));
        //         }
        //     } catch (Exception $e) {
        //         // Don't fail the entire transaction if fund_transactions insert fails
        //         error_log("Warning: Could not add fund transaction record: " . $e->getMessage());
        //     }
        // }
    } else if ($refund_method === 'Cash') {
        // Log cash refund transaction
        transaction_log('Cash Refund', "Sales return for invoice #$invoice_number", -$total_return_amount);
    }
    
    // Commit the transaction
    mysqli_commit($con);
    
    // Log the successful return
    transaction_log('Sales Return', "Processed return for invoice #$invoice_number", -$total_return_amount);

    // Update the cash register if a session is open
    updateCashRegister($total_return_amount, $refund_method);

    echo json_encode([
        'success' => true,
        'message' => 'Return processed successfully',
        'return_id' => $return_id
    ]);
    
} catch (Exception $e) {
    // Rollback the transaction on error
    mysqli_rollback($con);
    
    // Log the error
    error_log("Sales return error: " . $e->getMessage());
    
    // Get SQL errors if available
    $sql_error = mysqli_error($con);
    $sql_errno = mysqli_errno($con);
    
    // Create detailed error response
    $error_details = [
        'error_message' => $e->getMessage(),
        'sql_error' => $sql_error,
        'sql_errno' => $sql_errno,
        'request_data' => $input
    ];
    
    echo json_encode([
        'success' => false,
        'message' => 'Error processing return: ' . $e->getMessage(),
        'error_details' => $error_details
    ]);
}

/**
 * Updates the cash register records when a return is processed
 * 
 * @param float $return_amount The total return amount
 * @param string $refund_method The refund method (Cash, Store Credit)
 */
function updateCashRegister($return_amount, $refund_method) {
    global $con;
    
    // Only update the cash register for cash refunds
    if ($refund_method !== 'Cash') {
        return;
    }
    
    // Check if there's an open cash register session
    $register_query = "SELECT id FROM cash_register WHERE closed_at IS NULL ORDER BY id DESC LIMIT 1";
    $register_result = mysqli_query($con, $register_query);
    
    if (!$register_result || mysqli_num_rows($register_result) === 0) {
        // No open cash register session found, log this
        error_log("Cash return processed but no open cash register session found. Return amount: " . $return_amount);
        return;
    }
    
    $register = mysqli_fetch_assoc($register_result);
    $register_id = $register['id'];
    
    // Log the relationship between the return and the cash register
    error_log("Cash return of " . $return_amount . " processed and associated with cash register ID: " . $register_id);
}
?>