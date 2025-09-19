<?php
header('Content-Type: application/json');
session_start();
require_once 'inc/config.php';

// CONFIGURATION: Get settings from database
// Default values in case database is not available
$sell_Insufficient_stock_item = 1;
$sell_Inactive_batch_products = 1;

// Fetch configuration values from settings table
try {
    $settings_query = "SELECT setting_name, setting_value FROM settings WHERE setting_name IN ('sell_Insufficient_stock_item', 'sell_Inactive_batch_products')";
    $settings_result = mysqli_query($con, $settings_query);
    
    if ($settings_result) {
        while ($setting = mysqli_fetch_assoc($settings_result)) {
            switch ($setting['setting_name']) {
                case 'sell_Insufficient_stock_item':
                    $sell_Insufficient_stock_item = intval($setting['setting_value']);
                    break;
                case 'sell_Inactive_batch_products':
                    $sell_Inactive_batch_products = intval($setting['setting_value']);
                    break;
            }
        }
        mysqli_free_result($settings_result);
    }
} catch (Exception $e) {
    // If settings table doesn't exist or query fails, use default values
    error_log("Warning: Could not load settings from database. Using default values. Error: " . $e->getMessage());
}

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

// Get current Logged in Employee User id with fallback
$biller = null;
if (isset($_SESSION['employee_id'])) {
    $biller = $_SESSION['employee_id'];
} else {
    // Fallback: Use biller from localStorage if session expired
    if (isset($data['fallbackBillerId']) && !empty($data['fallbackBillerId'])) {
        $biller = intval($data['fallbackBillerId']);
        
        // Verify the employee still exists and is active
        $verify_query = "SELECT employ_id FROM employees WHERE employ_id = ? AND status = '1'";
        $verify_stmt = mysqli_prepare($con, $verify_query);
        if ($verify_stmt) {
            mysqli_stmt_bind_param($verify_stmt, 'i', $biller);
            mysqli_stmt_execute($verify_stmt);
            $verify_result = mysqli_stmt_get_result($verify_stmt);
            if (mysqli_num_rows($verify_result) === 0) {
                $biller = null; // Employee not found or inactive
            }
            mysqli_stmt_close($verify_stmt);
        }
    }
    
    if (!$biller) {
        $errors[] = "User session expired. Please login again.";
    }
}

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
$printType = isset($data['printType']) ? trim($data['printType']) : 'receipt';
if (!in_array($printType, ['receipt', 'standard'])) {
    $printType = 'receipt'; // Default to receipt if invalid type
}
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

// Initialize total cost and profit for invoice
$totalInvoiceCost = 0;
$totalInvoiceProfit = 0;

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

    // Insert invoice with basic information (cost and profit will be updated after product processing)
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

    // DEBUG: Log values before invoice insertion
    // error_log("=== INVOICE INSERTION DEBUG ===");
    // error_log("Customer Name: " . $customerName);
    // error_log("Customer Number: " . $customerNumber);
    // error_log("Customer ID: " . $customerId);
    // error_log("Subtotal: " . $subtotal);
    // error_log("Discount: " . $discount);
    // error_log("Amount Received: " . $amount_received);
    // error_log("Cash Change: " . $cash_change);
    // error_log("Advance: " . $advance);
    // error_log("Balance: " . $balance);
    // error_log("Payment Method: " . $paymentMethod);
    // error_log("Full Paid: " . $fullPaid);
    // error_log("Invoice Description: " . $invoiceDescription);
    // error_log("Biller: " . $biller);
    // error_log("Individual Discount Mode: " . $individual_discount_mode_int);
    // error_log("Credit Payment: " . $credit_payment_int);
    // error_log("NOTE: Cost and profit will be calculated and updated after product processing");
    // error_log("=== END INVOICE INSERTION DEBUG ===");

    mysqli_stmt_bind_param(
        $stmt,
        'ssidddddsissiii',
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

    // Update product stock - configurable stock validation
    function updateStock($con, $batch_id, $quantity, $productName = '', $sell_Insufficient_stock_item = 0)
    {
        if ($sell_Insufficient_stock_item == 0) {
            // Stock validation enabled - check if there's enough stock
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
        }
        
        // Update the stock (quantity can go negative if $sell_Insufficient_stock_item = 1)
        $query = "UPDATE product_batch SET quantity = quantity - ? WHERE batch_id = ?";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing product stock update query");
        }

        mysqli_stmt_bind_param($stmt, 'ds', $quantity, $batch_id);
        mysqli_stmt_execute($stmt);
        
        if ($sell_Insufficient_stock_item == 0) {
            // Only verify update if stock validation is enabled
            if (mysqli_affected_rows($con) !== 1) {
                throw new Exception("Failed to update stock for batch: " . $batch_id);
            }
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
        
        // Check if this is a one-time product
        $isOneTimeProduct = isset($product['isOneTimeProduct']) && $product['isOneTimeProduct'] === true;
        
        // Extract pricing information
        $regularPrice = isset($product['regular_price']) ? floatval($product['regular_price']) : 
                      (isset($product['price']) ? floatval($product['price']) : 0);
        $discountPrice = isset($product['discount_price']) ? floatval($product['discount_price']) : $regularPrice;
        
        // Calculate pricing based on the mode
        $price = $individualDiscountMode ? $discountPrice : $regularPrice;
        $amount = $quantity * $price;
        
        // Debug logging for one-time products
        if ($isOneTimeProduct) {
            // error_log("One-time product debug - Product: $productName, Quantity: $quantity, Regular Price: $regularPrice, Discount Price: $discountPrice, Amount: $amount, Individual Discount Mode: " . ($individualDiscountMode ? 'true' : 'false'));
        }
        
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
        
        // Accumulate total cost and profit for invoice
        $totalInvoiceCost += ($cost * $quantity);
        $totalInvoiceProfit += $profit;
        
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
                updateStock($con, $batch_id, $quantity, $productName, $sell_Insufficient_stock_item);
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

                    // Build batch query based on configuration variables
                    $statusCondition = ($sell_Inactive_batch_products == 1) ? "" : "AND status = 'active'";
                    
                    if ($sell_Insufficient_stock_item == 1) {
                        // Allow out-of-stock sales: Find batch regardless of quantity
                        $batchQuery = "SELECT batch_id FROM product_batch 
                                       WHERE product_id = ? $statusCondition 
                                       ORDER BY expiry_date ASC, restocked_at ASC LIMIT 1";
                        $batchStmt = mysqli_prepare($con, $batchQuery);
                        mysqli_stmt_bind_param($batchStmt, 'i', $row['component_product_id']);
                        mysqli_stmt_execute($batchStmt);
                        mysqli_stmt_bind_result($batchStmt, $componentBatchID);
                        $batchFound = mysqli_stmt_fetch($batchStmt);
                        mysqli_stmt_close($batchStmt);

                        if ($batchFound && $componentBatchID) {
                            updateStock($con, $componentBatchID, $totalQtyNeeded, $row['product_name'], $sell_Insufficient_stock_item);
                        } else {
                            $batchType = ($sell_Inactive_batch_products == 1) ? "any" : "active";
                            error_log("Warning: No $batchType batch found for combo component: " . $row['product_name'] . " (Product ID: " . $row['component_product_id'] . ")");
                        }
                    } else {
                        // Stock validation enabled: Find suitable batch with enough quantity
                        $batchQuery = "SELECT batch_id FROM product_batch 
                                       WHERE product_id = ? $statusCondition AND quantity >= ? 
                                       ORDER BY expiry_date ASC, restocked_at ASC LIMIT 1";
                        $batchStmt = mysqli_prepare($con, $batchQuery);
                        mysqli_stmt_bind_param($batchStmt, 'id', $row['component_product_id'], $totalQtyNeeded);
                        mysqli_stmt_execute($batchStmt);
                        mysqli_stmt_bind_result($batchStmt, $componentBatchID);
                        $batchFound = mysqli_stmt_fetch($batchStmt);
                        mysqli_stmt_close($batchStmt);

                        if ($batchFound && $componentBatchID) {
                            updateStock($con, $componentBatchID, $totalQtyNeeded, $row['product_name'], $sell_Insufficient_stock_item);
                        } else {
                            $batchType = ($sell_Inactive_batch_products == 1) ? "any" : "active";
                            throw new Exception("Insufficient stock for component: " . $row['product_name'] . " (No $batchType batch with sufficient quantity found)");
                        }
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

    // Update invoice with calculated cost and profit totals
    // error_log("=== UPDATING INVOICE WITH CALCULATED TOTALS ===");
    // error_log("Final Total Invoice Cost: " . $totalInvoiceCost);
    // error_log("Final Total Invoice Profit: " . $totalInvoiceProfit);
    
    $updateInvoiceQuery = "UPDATE invoice SET cost = ?, profit = ? WHERE invoice_number = ?";
    $updateStmt = mysqli_prepare($con, $updateInvoiceQuery);
    if (!$updateStmt) {
        throw new Exception("Error preparing invoice update query: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($updateStmt, 'ddi', $totalInvoiceCost, $totalInvoiceProfit, $invoiceNumber);
    mysqli_stmt_execute($updateStmt);
    
    if (mysqli_affected_rows($con) !== 1) {
        // error_log("WARNING: Invoice update affected " . mysqli_affected_rows($con) . " rows");
    } else {
        // error_log("SUCCESS: Invoice cost and profit updated successfully");
    }
    mysqli_stmt_close($updateStmt);

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

    // DEBUGGING: Log all database insertions
    error_log("=== INVOICE SUBMISSION DEBUG INFO ===");
    error_log("Invoice Number: " . $invoiceNumber);
    error_log("Total Invoice Cost: " . $totalInvoiceCost);
    error_log("Total Invoice Profit: " . $totalInvoiceProfit);
    error_log("Customer: " . $customerName . " (" . $customerNumber . ")");
    error_log("Subtotal: " . $subtotal);
    error_log("Discount: " . $discount);
    error_log("Final Total: " . ($subtotal - $discount));
    error_log("Amount Received: " . $amount_received);
    error_log("Cash Change: " . $cash_change);
    error_log("Advance: " . $advance);
    error_log("Balance: " . $balance);
    
    // Verify what was actually inserted into invoice table
    $verifyQuery = "SELECT invoice_number, total, discount, cost, profit, customer_name, biller 
                    FROM invoice WHERE invoice_number = ?";
    $verifyStmt = mysqli_prepare($con, $verifyQuery);
    if ($verifyStmt) {
        mysqli_stmt_bind_param($verifyStmt, 'i', $invoiceNumber);
        mysqli_stmt_execute($verifyStmt);
        $verifyResult = mysqli_stmt_get_result($verifyStmt);
        if ($row = mysqli_fetch_assoc($verifyResult)) {
            error_log("ACTUAL INVOICE DB DATA:");
            error_log("- Invoice Number: " . $row['invoice_number']);
            error_log("- Total: " . $row['total']);
            error_log("- Discount: " . $row['discount']);
            error_log("- Cost: " . $row['cost']);
            error_log("- Profit: " . $row['profit']);
            error_log("- Customer: " . $row['customer_name']);
            error_log("- Biller: " . $row['biller']);
        } else {
            error_log("ERROR: Could not verify invoice data in database");
        }
        mysqli_stmt_close($verifyStmt);
    }
    
    // Verify sales records
    $salesQuery = "SELECT product, qty, cost, profit, amount FROM sales WHERE invoice_number = ?";
    $salesStmt = mysqli_prepare($con, $salesQuery);
    if ($salesStmt) {
        mysqli_stmt_bind_param($salesStmt, 'i', $invoiceNumber);
        mysqli_stmt_execute($salesStmt);
        $salesResult = mysqli_stmt_get_result($salesStmt);
        $salesCount = 0;
        $totalSalesCost = 0;
        $totalSalesProfit = 0;
        while ($salesRow = mysqli_fetch_assoc($salesResult)) {
            $salesCount++;
            $totalSalesCost += $salesRow['cost'] * $salesRow['qty'];
            $totalSalesProfit += $salesRow['profit'];
            error_log("SALES RECORD " . $salesCount . ": " . $salesRow['product'] . 
                     " - Qty: " . $salesRow['qty'] . 
                     ", Cost: " . $salesRow['cost'] . 
                     ", Profit: " . $salesRow['profit'] . 
                     ", Amount: " . $salesRow['amount']);
        }
        error_log("TOTAL SALES RECORDS: " . $salesCount);
        error_log("CALCULATED TOTAL SALES COST: " . $totalSalesCost);
        error_log("CALCULATED TOTAL SALES PROFIT: " . $totalSalesProfit);
        mysqli_stmt_close($salesStmt);
    }
    error_log("=== END DEBUG INFO ===");

    // Commit transaction
    mysqli_commit($con);    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Invoice submitted successfully',
        'invoiceNumber' => $invoiceNumber,
        'printReceipt' => $printReceipt,
        'printType' => $printType
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
    }    // Return error to client
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'code' => 500,
        'session_expired' => !isset($_SESSION['employee_id']) // Help client identify session issues
    ]);
}

// Always close the database connection
mysqli_close($con);
