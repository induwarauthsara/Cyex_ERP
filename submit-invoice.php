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

$response = [];
$errors = [];

// Validate required fields
$totalPayable = $data['totalPayable'] ?? 0;
$totalReceived = $data['totalReceived'] ?? 0;
$customerName = $data['customerName'] ?? 'Walk-in Customer';
$customerNumber = $data['customerNumber'] ?? '0';

// Calculate discount and final totals
$subtotal = $data['subtotal'];
$discountValue = $data['discountValue'] ?? 0;
$discountType = $data['discountType'];
$discount = $discountType === 'percentage' ? ($subtotal * $discountValue / 100) : $discountValue;
$finalTotal = $subtotal - $discount;

$balance = $data['balance']; // Api customer ta denna thiyena balance eka.
$paymentMethod = $data['paymentMethod'] ?? 'Cash';
$printReceipt = $data['printReceipt'] ?? false;
$extraPaidAmount = $data['extraPaidAmount'] ?? 0;
$bool_extraPaidAddToCustomerFund = $data['extraPaidAddToCustomerFund'] ?? false;
$quickCashCounts = json_encode($data['quickCashCounts'] ?? []);

$useCustomerExtraFundAmount = $data['useCustomerExtraFundAmount'] ?? 0;
$bool_useCustomerExtraFund = $data['bool_useCustomerExtraFund'] ?? false;
$bool_creditPayment = $data['creditPayment'] ?? false;

// Customer extra fund validation and correct the total payable amount calculation
if ($bool_useCustomerExtraFund) {
    $totalPayable -=  $useCustomerExtraFundAmount;
}

if ($totalReceived < $totalPayable && !$bool_creditPayment) {
    $errors[] = "Insufficient amount received.";
}

$productList = $data['productList'] ?? [];
if (empty($productList)) {
    $errors[] = "No products found in the invoice.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(", ", $errors)]);
    exit;
}



// Start database transaction
mysqli_begin_transaction($con);

try {
    // Check if customer exists
    $query = "SELECT id FROM customers WHERE customer_name = ? AND customer_mobile = ?";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Error preparing customer query: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'ss', $customerName, $customerNumber);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $customerId = null;
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $customerId);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);

    if (!$customerId) {
        $query = "INSERT INTO customers (customer_name, customer_mobile, customer_type) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing customer insert query: " . mysqli_error($con));
        }
        $customerType = 'regular';
        mysqli_stmt_bind_param($stmt, 'sss', $customerName, $customerNumber, $customerType);
        mysqli_stmt_execute($stmt);
        $customerId = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);
    }

    // Insert invoice
    $query = "INSERT INTO invoice (customer_name, customer_mobile, total, discount, advance, balance, paymentMethod, full_paid, invoice_description, biller)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Error preparing invoice insert query: " . mysqli_error($con));
    }

    $invoiceDescription = 'Standard Purchase';
    $fullPaid = ($balance <= 0) ? 1 : 0;
    $biller = $data['cashierName'] ?? 'Unknown';
    $advance = $totalReceived - $balance;

    mysqli_stmt_bind_param(
        $stmt,
        'ssddddsiss',
        $customerName,
        $customerNumber,
        $subtotal,
        $discount,
        $advance,
        $balance,
        $paymentMethod,
        $fullPaid,
        $invoiceDescription,
        $biller
    );

    mysqli_stmt_execute($stmt);
    $invoiceNumber = mysqli_insert_id($con);
    mysqli_stmt_close($stmt);

    //bool_useCustomerExtraFund
    if ($bool_useCustomerExtraFund) {
        $query = "UPDATE customers SET customer_extra_fund = customer_extra_fund - ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing customer fund update query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, 'di', $useCustomerExtraFundAmount, $customerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    //bool_extraPaidAddToCustomerFund
    if ($bool_extraPaidAddToCustomerFund) {
        $query = "UPDATE customers SET customer_extra_fund = customer_extra_fund + ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing customer fund update query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, 'di', $extraPaidAmount, $customerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $cashAmount = $data['cashAmount'];
    } else {
        $cashAmount = $data['cashAmount'] - $extraPaidAmount; // Remove extra paid amount from cash amount (Salli ithuru dunna ewa eya dunna ganen adu karanawa)
    }

    // Insert Payment Amounts to Accounts
    $cardAmount = $data['cardAmount'];
    $bankAmount = $data['bankAmount'];

    // accounts table = account name (cash_in_hand, card_payment, online_transaction), amount
    $accounts = [
        ['cash_in_hand', $cashAmount],
        ['card_payment', $cardAmount],
        ['online_transaction', $bankAmount]
    ];

    // Update accounts balance
    foreach ($accounts as $account) {
        $accountName = $account[0];
        $amount = $account[1];

        // check amount have
        if ($amount > 0) {
            $query = "UPDATE accounts SET amount = amount + ? WHERE account_name = ?";
            $stmt = mysqli_prepare($con, $query);
            if (!$stmt) {
                throw new Exception("Error preparing accounts update query: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($stmt, 'ds', $amount, $accountName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Update product stock reusable - function
    function updateStock($con, $batch_id, $quantity)
    {
        $query = "UPDATE product_batch SET quantity = quantity - ? WHERE batch_id = ?";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing product stock update query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, 'is', $quantity, $batch_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    // Insert sales records
    foreach ($productList as $product) {
        $query = "INSERT INTO sales (invoice_number, product, batch, qty, rate, amount, cost, profit, worker)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        if (!$stmt) {
            throw new Exception("Error preparing sales insert query: " . mysqli_error($con));
        }

        $productName = $product['name'];
        $quantity = $product['quantity'];
        $price = $product['price'];
        $amount = $quantity * $price;
        $cost = 0; // You need to fetch this from your product or batch table
        $profit = $amount - ($cost * $quantity);
        $worker = $biller; // Using the cashier as the worker
        $batch_id = $product['batch_id'];

        mysqli_stmt_bind_param($stmt, 'issddddds', $invoiceNumber, $productName, $batch_id, $quantity, $price, $amount, $cost, $profit, $worker);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Check product type. if product type is service or digital, then no need to update stock. if combo product, then update stock for each product in combo product. if normal product, then update stock.
        $stmt = mysqli_prepare($con, "SELECT product_type FROM products WHERE product_name = ?");
        if (!$stmt) throw new Exception("Error preparing query: " . mysqli_error($con));

        mysqli_stmt_bind_param($stmt, 's', $productName);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $productType);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);



        // Update stock according to product type
        // standard, combo, service, digital
        // for standard product, update stock
        if ($productType === 'standard') {
            updateStock($con, $batch_id, $quantity);
        }


        // for combo product, update stock for each product in combo product
        if ($productType === 'combo') {
            // Find This Combo Product's ID
            $query = "SELECT product_id FROM products WHERE product_name = ?";
            $stmt = mysqli_prepare($con, $query);
            if (!$stmt) {
                throw new Exception("Error preparing combo product id query: " . mysqli_error($con));
            }
            mysqli_stmt_bind_param($stmt, 's', $productName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $comboProductId);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // combo_products(id,combo_product_id, component_product_id, quantity, created_at)
            // product_batch(batch_id, product_id, batch_number, cost, selling_price, profit, expiry_date, quantity, supplier_id, purchase_date, status, notes, created_at, updated_at, restocked_at) 
            // need to connect product id and name to get combo product details
            // for each component_product_id find priority batch number (priority batch = active, oldersted restock date, max qty available) and update stock

            // Get all component products for this combo
            $query = "SELECT cp.component_product_id, cp.quantity as required_qty, p.product_name 
                      FROM combo_products cp 
                      JOIN products p ON p.product_id = cp.component_product_id 
                      WHERE cp.combo_product_id = ?";
            $stmt = mysqli_prepare($con, $query);
            if (!$stmt) {
                throw new Exception("Error preparing combo components query: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($stmt, 'i', $comboProductId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                // Calculate total quantity needed for this component
                $totalQtyNeeded = $row['required_qty'] * $quantity;

                // Find suitable batch with enough quantity
                $batchQuery = "SELECT batch_id FROM product_batch 
                               WHERE product_id = ? AND status = 'active' AND quantity >= ? 
                               ORDER BY restocked_at ASC LIMIT 1";
                $batchStmt = mysqli_prepare($con, $batchQuery);
                mysqli_stmt_bind_param($batchStmt, 'id', $row['component_product_id'], $totalQtyNeeded);
                mysqli_stmt_execute($batchStmt);
                mysqli_stmt_bind_result($batchStmt, $componentBatchID);
                mysqli_stmt_fetch($batchStmt);
                mysqli_stmt_close($batchStmt);

                if ($componentBatchID) {
                    updateStock($con, $componentBatchID, $totalQtyNeeded);
                } else {
                    throw new Exception("Insufficient stock for component: " . $row['product_name']);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Invoice submitted successfully.',
        'invoiceNumber' => $invoiceNumber
    ]);
} catch (Exception $e) {
    // Rollback transaction if any error occurs
    mysqli_rollback($con);

    // Log error
    $employeeId = $_SESSION['employee_id'] ?? 0;
    $insertErrorLogQuery = "INSERT INTO error_log (error_code, error_message, query, action, action_description, employee_id, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $insertErrorLogQuery);
    if ($stmt) {
        $errorCode = 500;
        $errorMessage = $e->getMessage();
        $query = $query ?? '';
        $action = 'invoice_submission';
        $actionDescription = 'Error during invoice submission';
        $status = 'pending';

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

    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

mysqli_close($con);
