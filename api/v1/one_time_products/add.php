<?php
/**
 * Add One-Time Product API
 * 
 * Creates a custom/one-time product that isn't in regular inventory
 * POST /api/v1/one_time_products/add.php
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
    $required = ['product_name', 'rate', 'quantity'];
    $errors = [];
    
    foreach ($required as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    if (!empty($errors)) {
        ApiResponse::validationError('Validation failed', $errors);
    }
    
    // Sanitize and prepare data
    $product_name = mysqli_real_escape_string($con, trim($input['product_name']));
    $rate = floatval($input['rate']);
    $quantity = floatval($input['quantity']);
    $description = isset($input['description']) ? mysqli_real_escape_string($con, trim($input['description'])) : '';
    $amount = $rate * $quantity;
    
    // Validate numeric values
    if ($rate <= 0) {
        ApiResponse::error('Rate must be greater than zero', 422);
    }
    
    if ($quantity <= 0) {
        ApiResponse::error('Quantity must be greater than zero', 422);
    }
    
    // Create one-time product entry
    // Note: This creates an uncleared one-time product that will be associated with an invoice
    $query = "INSERT INTO oneTimeProducts_sales (
        product, 
        qty, 
        rate, 
        amount, 
        status,
        worker,
        invoice_number
    ) VALUES (?, ?, ?, ?, 'uncleared', ?, 0)";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . mysqli_error($con));
    }
    
    $worker_name = $user['name'];
    mysqli_stmt_bind_param($stmt, 'sddds', 
        $product_name, 
        $quantity, 
        $rate, 
        $amount,
        $worker_name
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error creating one-time product: " . mysqli_stmt_error($stmt));
    }
    
    $product_id = mysqli_insert_id($con);
    
    // Return success with product details
    ApiResponse::success([
        'product_id' => $product_id,
        'product_name' => $product_name,
        'rate' => $rate,
        'quantity' => $quantity,
        'amount' => $amount,
        'description' => $description,
        'status' => 'uncleared'
    ], 'One-time product created successfully', 201);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to create one-time product', 500);
    }
}
