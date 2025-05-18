<?php
header('Content-Type: application/json');
session_start();
require_once 'inc/config.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data === null) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON data received"
    ]);
    exit;
}

// Initialize variables and validation arrays
$response = [];
$errors = [];

// Sanitize and validate required fields
$totalPayable = isset($data['totalPayable']) ? floatval($data['totalPayable']) : 0;
$totalReceived = isset($data['totalReceived']) ? floatval($data['totalReceived']) : 0;
$customerName = isset($data['customerName']) ? trim($data['customerName']) : 'Walk-in Customer';
$customerNumber = isset($data['customerNumber']) ? trim($data['customerNumber']) : '1';
$individualDiscountMode = isset($data['individualDiscountMode']) ? (bool)$data['individualDiscountMode'] : false;
$bool_creditPayment = isset($data['creditPayment']) ? (bool)$data['creditPayment'] : false;

// Get current Logged in Employee User id
$biller = $_SESSION['employee_id'];

// Calculate discount and final totals
$subtotal = isset($data['subtotal']) ? floatval($data['subtotal']) : 0;
if ($subtotal <= 0) {
    $errors[] = "Invalid subtotal amount";
}

$discountValue = isset($data['discountValue']) ? floatval($data['discountValue']) : 0;
$discountType = isset($data['discountType']) ? trim($data['discountType']) : 'flat';
if (!in_array($discountType, ['flat', 'percentage'])) {
    $discountType = 'flat'; // Default to flat if invalid type
}

$discount = $discountType === 'percentage' ? ($subtotal * $discountValue / 100) : $discountValue;
$finalTotal = $subtotal - $discount;

// New calculation for amount_received (previously advance), cash_change (previously balance), 
// the new advance, and the new balance
$amount_received = isset($data['totalReceived']) ? floatval($data['totalReceived']) : 0;
$cash_change = 0;
$advance = 0;
$balance = 0;

// Get customer fund data
$useCustomerExtraFundAmount = isset($data['useCustomerExtraFundAmount']) ? floatval($data['useCustomerExtraFundAmount']) : 0;
$bool_useCustomerExtraFund = isset($data['bool_useCustomerExtraFund']) ? (bool)$data['bool_useCustomerExtraFund'] : false;

// Calculate cash_change (amount to return to customer when they pay more than needed)
if ($amount_received > $finalTotal && !$bool_creditPayment) {
    // If customer overpaid
    $cash_change = $amount_received - $finalTotal;
    // Advance is what customer actually paid minus change returned (not including extra fund)
    $advance = $amount_received - $cash_change; 
    $balance = 0; // No remaining balance
} else {
    $cash_change = 0;
    // Advance is just what they actually handed over (not including extra fund)
    $advance = $amount_received;
    $balance = $finalTotal - $advance; // Balance is what's still due
}

// Customer extra fund handling - only reduces balance, doesn't increase advance
if ($bool_useCustomerExtraFund && $useCustomerExtraFundAmount > 0) {
    // Extra fund only reduces the balance, not counted as advance
    $balance = $balance - $useCustomerExtraFundAmount;
    if ($balance < 0) {
        $balance = 0; // Ensure balance doesn't go negative
    }
    // Note: We don't increase advance here since customer extra fund isn't handed over now
}

$paymentMethod = isset($data['paymentMethod']) ? trim($data['paymentMethod']) : 'Cash';
if (!in_array($paymentMethod, ['Cash', 'Card', 'Online Transfer', 'Credit'])) {
    $paymentMethod = 'Cash'; // Default to Cash if invalid method
}

$printReceipt = isset($data['printReceipt']) ? (bool)$data['printReceipt'] : false;
$extraPaidAmount = isset($data['extraPaidAmount']) ? floatval($data['extraPaidAmount']) : 0;
$bool_extraPaidAddToCustomerFund = isset($data['extraPaidAddToCustomerFund']) ? (bool)$data['extraPaidAddToCustomerFund'] : false;

// Validate Quick Cash counts
$quickCashData = $data['quickCashCounts'] ?? [];
if (!is_array($quickCashData)) {
    $quickCashData = [];
}
$quickCashCounts = json_encode($quickCashData);

// Payment validation - now checking against the updated balance
if ($balance > 0 && !$bool_creditPayment) {
    $errors[] = "Insufficient amount received";
}

// Product list validation
$productList = isset($data['productList']) ? $data['productList'] : [];
if (!is_array($productList) || empty($productList)) {
    $errors[] = "No products found in the invoice";
}

// Exit early if there are validation errors
if (!empty($errors)) {
    echo json_encode([
        'success' => false, 
        'message' => implode(", ", $errors)
    ]);
    exit;
}

// Start database transaction
mysqli_begin_transaction($con);



try {
    // Check if customer exists and get or create customer ID
    $query = "SELECT id, customer_extra_fund FROM customers WHERE customer_name = ? AND customer_mobile = ?";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Error preparing customer query: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'ss', $customerName, $customerNumber);
    mysqli_stmt_execute($stmt);
    $customerResult = mysqli_stmt_get_result($stmt);
    $customerId = null;
    $currentExtraFund = 0;
    
    if ($row = mysqli_fetch_assoc($customerResult)) {
        $customerId = $row['id'];
        $currentExtraFund = floatval($row['customer_extra_fund'] ?? 0);
    }
    mysqli_stmt_close($stmt);

    // Create customer if not found and not a walk-in customer
    if (!$customerId && $customerName != 'Walk-in Customer') {
        $query = "INSERT INTO customers (customer_name, customer_mobile, customer_type, customer_extra_fund) 
                  VALUES (?, ?, ?, 0)";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing customer insert query: " . mysqli_error($con));
        }
        
        $customerType = 'regular';
        mysqli_stmt_bind_param($stmt, 'sss', $customerName, $customerNumber, $customerType);
        mysqli_stmt_execute($stmt);
        $customerId = mysqli_insert_id($con);
        
        if (!$customerId && $customerName != 'Walk-in Customer') {
            throw new Exception("Failed to create customer record");
        }
        mysqli_stmt_close($stmt);
    }

    // Verify extra fund amount is available if using it
    if ($bool_useCustomerExtraFund && $useCustomerExtraFundAmount > $currentExtraFund) {
        throw new Exception("Customer does not have enough funds. Available: " . $currentExtraFund);
    }

    // Insert invoice with updated column structure
    $query = "INSERT INTO invoice (
                customer_name, customer_mobile, customer_id, total, discount, amount_received, 
                cash_change, advance, balance, paymentMethod, full_paid, invoice_description, biller,
                individual_discount_mode, credit_payment, time, invoice_date
              ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
              )";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Error preparing invoice insert query: " . mysqli_error($con));
    }

    $invoiceDescription = 'Standard Purchase';
    $fullPaid = ($balance <= 0 || $bool_creditPayment) ? ($bool_creditPayment ? 0 : 1) : 0;
    $individual_discount_mode_int = $individualDiscountMode ? 1 : 0;
    $credit_payment_int = $bool_creditPayment ? 1 : 0;

    mysqli_stmt_bind_param(
        $stmt,
        'ssidddddsssiiii',
        $customerName,
        $customerNumber,
        $customerId,
        $subtotal,
        $discount,
        $amount_received,
        $cash_change,
        $advance,
        $balance,
        $paymentMethod,
        $fullPaid,
        $invoiceDescription,
        $biller,
        $individual_discount_mode_int,
        $credit_payment_int
    );

    mysqli_stmt_execute($stmt);
    $invoiceNumber = mysqli_insert_id($con);
    
    if (!$invoiceNumber) {
        throw new Exception("Failed to create invoice record");
    }
    mysqli_stmt_close($stmt);

    // Handle customer extra fund if using it
    if ($bool_useCustomerExtraFund && $useCustomerExtraFundAmount > 0 && $customerId) {
        $query = "UPDATE customers SET customer_extra_fund = customer_extra_fund - ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing customer fund update query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, 'di', $useCustomerExtraFundAmount, $customerId);
        mysqli_stmt_execute($stmt);
        
        // Check if update was successful
        if (mysqli_affected_rows($con) !== 1) {
            throw new Exception("Failed to update customer's extra fund");
        }
        mysqli_stmt_close($stmt);
        
        // Check if fund_transactions table exists before using it
        $fund_transactions_check = mysqli_query($con, "SHOW TABLES LIKE 'fund_transactions'");
        if (mysqli_num_rows($fund_transactions_check) > 0) {
            $query = "INSERT INTO fund_transactions (customer_id, amount, type, description, invoice_id) 
                    VALUES (?, ?, 'deduction', 'Used for invoice payment', ?)";
            $stmt = mysqli_prepare($con, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'idi', $customerId, $useCustomerExtraFundAmount, $invoiceNumber);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }

    // Handle customer extra paid amount to fund
    $cashAmount = isset($data['cashAmount']) ? floatval($data['cashAmount']) : 0;
    
    if ($bool_extraPaidAddToCustomerFund && $extraPaidAmount > 0 && $customerId) {
        $query = "UPDATE customers SET customer_extra_fund = customer_extra_fund + ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing customer fund update query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, 'di', $extraPaidAmount, $customerId);
        mysqli_stmt_execute($stmt);
        
        // Check if update was successful
        if (mysqli_affected_rows($con) !== 1) {
            throw new Exception("Failed to update customer's extra fund with overpayment");
        }
        mysqli_stmt_close($stmt);
        
        // Check if fund_transactions table exists before using it
        $fund_transactions_check = mysqli_query($con, "SHOW TABLES LIKE 'fund_transactions'");
        if (mysqli_num_rows($fund_transactions_check) > 0) {
            $query = "INSERT INTO fund_transactions (customer_id, amount, type, description, invoice_id) 
                    VALUES (?, ?, 'addition', 'Extra payment added to fund', ?)";
            $stmt = mysqli_prepare($con, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'idi', $customerId, $extraPaidAmount, $invoiceNumber);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    } else if ($extraPaidAmount > 0) {
        // If extra paid amount exists but not adding to customer fund, adjust cash amount
        $cashAmount -= $extraPaidAmount;
    }

    // Insert Payment Amounts to Accounts
    $cardAmount = isset($data['cardAmount']) ? floatval($data['cardAmount']) : 0;
    $bankAmount = isset($data['bankAmount']) ? floatval($data['bankAmount']) : 0;

    // Update accounts balance with transaction reference
    $accounts = [
        ['cash_in_hand', $cashAmount],
        ['card_payment', $cardAmount],
        ['online_transaction', $bankAmount]
    ];

    foreach ($accounts as $account) {
        $accountName = $account[0];
        $amount = $account[1];

        if ($amount > 0) {
            // First check if account exists
            $checkQuery = "SELECT COUNT(*) FROM accounts WHERE account_name = ?";
            $stmt = mysqli_prepare($con, $checkQuery);
            mysqli_stmt_bind_param($stmt, 's', $accountName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $accountExists);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            if (!$accountExists) {
                // Create account if it doesn't exist
                $createQuery = "INSERT INTO accounts (account_name, amount) VALUES (?, 0)";
                $stmt = mysqli_prepare($con, $createQuery);
                mysqli_stmt_bind_param($stmt, 's', $accountName);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            // Update account balance
            $query = "UPDATE accounts SET amount = amount + ? WHERE account_name = ?";
            $stmt = mysqli_prepare($con, $query);
            if (!$stmt) {
                throw new Exception("Error preparing accounts update query: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($stmt, 'ds', $amount, $accountName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // Check if account_transactions table exists before using it
            $account_transactions_check = mysqli_query($con, "SHOW TABLES LIKE 'account_transactions'");
            if (mysqli_num_rows($account_transactions_check) > 0) {
                $transactionQuery = "INSERT INTO account_transactions (account_name, amount, type, description, reference) 
                                   VALUES (?, ?, 'credit', 'Sale payment', ?)";
                $stmt = mysqli_prepare($con, $transactionQuery);
                if ($stmt) {
                    $reference = "INV-" . $invoiceNumber;
                    mysqli_stmt_bind_param($stmt, 'sds', $accountName, $amount, $reference);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    // Update product stock - improved function to handle stock validation
    function updateStock($con, $batch_id, $quantity, $productName = '')
    {
        // First check if there's enough stock
        $checkQuery = "SELECT quantity FROM product_batch WHERE batch_id = ?";
        $stmt = mysqli_prepare($con, $checkQuery);
        if (!$stmt) {
            throw new Exception("Error checking stock for batch");
        }
        
        mysqli_stmt_bind_param($stmt, 's', $batch_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $currentStock);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if ($currentStock < $quantity) {
            throw new Exception("Insufficient stock for product: " . $productName . ", Batch: " . $batch_id . 
                               " (Requested: " . $quantity . ", Available: " . $currentStock . ")");
        }
        
        // Update the stock
        $query = "UPDATE product_batch SET quantity = quantity - ? WHERE batch_id = ?";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing product stock update query");
        }

        mysqli_stmt_bind_param($stmt, 'ds', $quantity, $batch_id);
        mysqli_stmt_execute($stmt);
        
        // Verify update was successful
        if (mysqli_affected_rows($con) !== 1) {
            throw new Exception("Failed to update stock for batch: " . $batch_id);
        }
        mysqli_stmt_close($stmt);
        
        return true;
    }
    
    // Insert sales records and update inventory
    foreach ($productList as $product) {
        // Validate product data
        if (!isset($product['name']) || !isset($product['quantity']) || !isset($product['batch_id'])) {
            throw new Exception("Invalid product data: Missing required fields");
        }
        
        $productName = trim($product['name']);
        $quantity = floatval($product['quantity']);
        $batch_id = trim($product['batch_id']);
        
        if (empty($productName) || $quantity <= 0 || empty($batch_id)) {
            throw new Exception("Invalid product data: Invalid values");
        }
        
        // Extract pricing information
        $regularPrice = isset($product['regular_price']) ? floatval($product['regular_price']) : 
                      (isset($product['price']) ? floatval($product['price']) : 0);
        $discountPrice = isset($product['discount_price']) ? floatval($product['discount_price']) : $regularPrice;
        
        // Calculate pricing based on the mode
        $price = $individualDiscountMode ? $discountPrice : $regularPrice;
        $amount = $quantity * $price;
        
        // Get product cost from batch if available
        $cost = 0;
        $costQuery = "SELECT cost FROM product_batch WHERE batch_id = ?";
        $stmt = mysqli_prepare($con, $costQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $batch_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $fetchedCost);
            if (mysqli_stmt_fetch($stmt)) {
                $cost = $fetchedCost ?: 0;
            }
            mysqli_stmt_close($stmt);
        }
        
        $profit = $amount - ($cost * $quantity);
        $individual_discount_mode_int = $individualDiscountMode ? 1 : 0;
        
        // Check if this is a one-time product
        $isOneTimeProduct = isset($product['isOneTimeProduct']) && $product['isOneTimeProduct'] === true;

        // Insert sales record
        $query = "INSERT INTO sales (
                    invoice_number, product, batch, qty, rate, discount_price, 
                    amount, cost, profit, worker, individual_discount_mode, datetime
                  ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
                  )";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing sales insert query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param(
            $stmt, 
            'issdddddsdi', 
            $invoiceNumber, 
            $productName, 
            $batch_id, 
            $quantity, 
            $regularPrice, 
            $discountPrice, 
            $amount, 
            $cost, 
            $profit, 
            $biller,
            $individual_discount_mode_int
        );
        
        mysqli_stmt_execute($stmt);
        if (mysqli_affected_rows($con) !== 1) {
            throw new Exception("Failed to record sales for product: " . $productName);
        }
        mysqli_stmt_close($stmt);
        
        // If this is a one-time product, also store in oneTimeProducts_sales table
        if ($isOneTimeProduct) {
            // Insert into oneTimeProducts_sales table
            $oneTimeQuery = "INSERT INTO oneTimeProducts_sales (
                invoice_number, product, qty, rate, amount, cost, profit, status, worker, regular_price, discount_price
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'uncleared', ?, ?, ?)";
            
            $stmt = mysqli_prepare($con, $oneTimeQuery);
            if (!$stmt) {
                throw new Exception("Error preparing oneTimeProducts_sales insert query: " . mysqli_error($con));
            }
            
            // Ensure quantity is treated as decimal
            $qtyDecimal = floatval($quantity);
            
            mysqli_stmt_bind_param(
                $stmt,
                'isddddssdd',
                $invoiceNumber,
                $productName,
                $qtyDecimal, // Use the decimal version
                $price, // rate is the price used for calculation (could be discount price)
                $amount,
                $cost,
                $profit,
                $biller,
                $regularPrice,
                $discountPrice
            );
            
            mysqli_stmt_execute($stmt);
            if (mysqli_affected_rows($con) !== 1) {
                throw new Exception("Failed to record one-time product sales for: " . $productName);
            }
            mysqli_stmt_close($stmt);
            
            // Skip stock updates for one-time products
            continue;
        }

        // Get product type to determine stock update strategy
        $stmt = mysqli_prepare($con, "SELECT product_id, product_type FROM products WHERE product_name = ?");
        if (!$stmt) throw new Exception("Error preparing product query: " . mysqli_error($con));

        mysqli_stmt_bind_param($stmt, 's', $productName);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $productId, $productType);
        $productFound = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if (!$productFound) {
            // Product not found - check if it should be treated as a one-time product
            if ($isOneTimeProduct) {
                // This was already explicitly marked as a one-time product and handled above
                continue;
            } else {
                // If product not found, automatically mark it as a one-time product
                $oneTimeQuery = "INSERT INTO oneTimeProducts_sales (
                    invoice_number, product, qty, rate, amount, cost, profit, status, worker, regular_price, discount_price
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'uncleared', ?, ?, ?)";
                
                $stmt = mysqli_prepare($con, $oneTimeQuery);
                if (!$stmt) {
                    throw new Exception("Error preparing oneTimeProducts_sales insert query: " . mysqli_error($con));
                }
                
                // Ensure quantity is treated as decimal
                $qtyDecimal = floatval($quantity);
                
                mysqli_stmt_bind_param(
                    $stmt,
                    'isddddssdd',
                    $invoiceNumber,
                    $productName,
                    $qtyDecimal,
                    $price,
                    $amount,
                    $cost,
                    $profit,
                    $biller,
                    $regularPrice,
                    $discountPrice
                );
                
                mysqli_stmt_execute($stmt);
                if (mysqli_affected_rows($con) !== 1) {
                    throw new Exception("Failed to record auto-detected one-time product for: " . $productName);
                }
                mysqli_stmt_close($stmt);
                
                // Continue to the next product - we don't need to update stock for one-time products
                continue;
            }
        }

        // Update stock according to product type (standard, combo, service, digital)
        switch ($productType) {
            case 'standard':
                // For standard products, update the stock directly
                updateStock($con, $batch_id, $quantity, $productName);
                break;
                
            case 'combo':
                // For combo products, update stock for each component
                $query = "SELECT cp.component_product_id, cp.quantity as required_qty, p.product_name 
                          FROM combo_products cp 
                          JOIN products p ON p.product_id = cp.component_product_id 
                          WHERE cp.combo_product_id = ?";
                $stmt = mysqli_prepare($con, $query);
                if (!$stmt) {
                    throw new Exception("Error preparing combo components query");
                }

                mysqli_stmt_bind_param($stmt, 'i', $productId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_assoc($result)) {
                    // Calculate total quantity needed for this component
                    $totalQtyNeeded = $row['required_qty'] * $quantity;

                    // Find suitable batch with enough quantity
                    $batchQuery = "SELECT batch_id FROM product_batch 
                                   WHERE product_id = ? AND status = 'active' AND quantity >= ? 
                                   ORDER BY expiry_date ASC, restocked_at ASC LIMIT 1";
                    $batchStmt = mysqli_prepare($con, $batchQuery);
                    mysqli_stmt_bind_param($batchStmt, 'id', $row['component_product_id'], $totalQtyNeeded);
                    mysqli_stmt_execute($batchStmt);
                    mysqli_stmt_bind_result($batchStmt, $componentBatchID);
                    $batchFound = mysqli_stmt_fetch($batchStmt);
                    mysqli_stmt_close($batchStmt);

                    if ($batchFound && $componentBatchID) {
                        updateStock($con, $componentBatchID, $totalQtyNeeded, $row['product_name']);
                    } else {
                        throw new Exception("Insufficient stock for component: " . $row['product_name']);
                    }
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'service':
            case 'digital':
                // No stock updates for service or digital products
                break;
                
            default:
                throw new Exception("Unknown product type: " . $productType);
        }
    }

    // Ensure payment_details table exists and insert payment details
    try {
        // Load the table creation script
        
        // Insert payment details
        $paymentMethod = $data['paymentMethod'] ?? 'Cash';
        $cashAmount = floatval($data['cashAmount'] ?? 0);
        $cardAmount = floatval($data['cardAmount'] ?? 0);
        $bankAmount = floatval($data['bankAmount'] ?? 0);
        
        $paymentQuery = "INSERT INTO payment_details (invoice_id, cash_amount, card_amount, bank_amount, payment_method) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $paymentQuery);
        if (!$stmt) {
            throw new Exception("Error preparing payment details query: " . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($stmt, 'iddds', $invoiceNumber, $cashAmount, $cardAmount, $bankAmount, $paymentMethod);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        // If we can't insert payment details, it's a serious issue, so let the transaction fail
        throw new Exception("Failed to save payment details: " . $e->getMessage());
    }

    // Commit transaction
    mysqli_commit($con);

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Invoice submitted successfully',
        'invoiceNumber' => $invoiceNumber,
        'printReceipt' => $printReceipt
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on any error
    mysqli_rollback($con);

    // Log error with better details
    $employeeId = $_SESSION['employee_id'] ?? 0;
    $errorDateTime = date('Y-m-d H:i:s');
    $insertErrorLogQuery = "INSERT INTO error_log (
                              error_code, error_message, query, action, 
                              action_description, employee_id, status
                            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            
    $stmt = mysqli_prepare($con, $insertErrorLogQuery);
    if ($stmt) {
        $errorCode = 500;
        $errorMessage = $e->getMessage();
        $query = isset($query) ? $query : 'Query not available';
        $action = 'invoice_submission';
        $actionDescription = 'Error during invoice submission';
        $status = 'pending';
        
        // Create error details for logging
        $errorDetails = "Customer: " . $customerName . " (" . $customerNumber . "), ";
        $errorDetails .= "Total: " . $totalPayable . ", ";
        $errorDetails .= "Products: " . count($productList) . " items";
        
        // Update action description to include details
        $actionDescription .= " - " . $errorDetails;

        mysqli_stmt_bind_param(
            $stmt,
            'issssss',
            $errorCode,
            $errorMessage,
            $query,
            $action,
            $actionDescription,
            $employeeId,
            $status
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Return error to client
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'code' => 500
    ]);
}

// Always close the database connection
mysqli_close($con);
