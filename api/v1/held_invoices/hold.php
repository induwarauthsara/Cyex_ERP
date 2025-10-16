<?php
/**
 * Hold Invoice API
 * 
 * Save current invoice/transaction for later processing
 * POST /api/v1/held_invoices/hold.php
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate user
$user = ApiAuth::requireAuth();

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['items'];
    $errors = [];
    
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    if (!empty($errors)) {
        ApiResponse::validationError('Validation failed', $errors);
    }
    
    if (empty($input['items']) || !is_array($input['items'])) {
        ApiResponse::error('At least one item is required', 422);
    }
    
    // Prepare hold data
    $customer_name = isset($input['customerName']) ? mysqli_real_escape_string($con, $input['customerName']) : 'Walk-in Customer';
    $customer_number = isset($input['customerNumber']) ? mysqli_real_escape_string($con, $input['customerNumber']) : '';
    $total_amount = isset($input['totalAmount']) ? floatval($input['totalAmount']) : 0;
    $discount_type = isset($input['discountType']) && in_array($input['discountType'], ['flat', 'percentage']) 
        ? $input['discountType'] : 'flat';
    $discount_value = isset($input['discountValue']) ? floatval($input['discountValue']) : 0;
    
    // Calculate discount amount
    $discount_amount = 0;
    if ($discount_value > 0) {
        if ($discount_type === 'percentage') {
            $discount_amount = ($total_amount * $discount_value) / 100;
        } else {
            $discount_amount = $discount_value;
        }
    }
    
    $total_payable = $total_amount - $discount_amount;
    $individual_discount_mode = isset($input['individualDiscountMode']) ? (int)$input['individualDiscountMode'] : 0;
    
    // Serialize items data
    $items_json = json_encode($input['items']);
    
    // Insert held invoice
    $query = "INSERT INTO held_invoices (
        customer_name,
        customer_number,
        items,
        total_amount,
        discount_amount,
        discount_type,
        discount_value,
        total_payable,
        individual_discount_mode,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'held')";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'sssddsddi', 
        $customer_name,
        $customer_number,
        $items_json,
        $total_amount,
        $discount_amount,
        $discount_type,
        $discount_value,
        $total_payable,
        $individual_discount_mode
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error holding invoice: " . mysqli_stmt_error($stmt));
    }
    
    $held_id = mysqli_insert_id($con);
    
    // Return success
    ApiResponse::success('Invoice held successfully', [
        'id' => $held_id,
        'customer_name' => $customer_name,
        'customer_number' => $customer_number,
        'item_count' => count($input['items']),
        'total_amount' => $total_amount,
        'discount_amount' => $discount_amount,
        'total_payable' => $total_payable
    ], 201);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to hold invoice', 500);
    }
}
