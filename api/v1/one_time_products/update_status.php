<?php
/**
 * Update One-Time Product Status API
 * 
 * Update status of a one-time product (uncleared/cleared/skip)
 * PUT /api/v1/one_time_products/update_status.php
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate user
$user = ApiAuth::requireAuth();

// Only allow PUT/POST method
if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['product_id']) || !isset($input['status'])) {
        ApiResponse::error('Product ID and status are required', 422);
    }
    
    $product_id = intval($input['product_id']);
    $status = mysqli_real_escape_string($con, $input['status']);
    
    // Validate status
    $valid_statuses = ['uncleared', 'cleared', 'skip'];
    if (!in_array($status, $valid_statuses)) {
        ApiResponse::error('Invalid status. Must be one of: ' . implode(', ', $valid_statuses), 422);
    }
    
    // Check if product exists
    $check_query = "SELECT oneTimeProduct_id, product, status FROM oneTimeProducts_sales WHERE oneTimeProduct_id = ?";
    $stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        ApiResponse::error('One-time product not found', 404);
    }
    
    $product = mysqli_fetch_assoc($result);
    
    // Update status
    $update_query = "UPDATE oneTimeProducts_sales SET status = ? WHERE oneTimeProduct_id = ?";
    $stmt = mysqli_prepare($con, $update_query);
    if (!$stmt) {
        throw new Exception("Error preparing update query: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'si', $status, $product_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error updating status: " . mysqli_stmt_error($stmt));
    }
    
    // Return success
    ApiResponse::success('Status updated successfully', [
        'product_id' => $product_id,
        'product_name' => $product['product'],
        'old_status' => $product['status'],
        'new_status' => $status,
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to update status', 500);
    }
}
