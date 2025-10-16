<?php
/**
 * Delete Held Invoice API
 * 
 * Delete/cancel a held invoice
 * DELETE /api/v1/held_invoices/delete.php
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate user
$user = ApiAuth::requireAuth();

// Only allow DELETE/POST method
if (!in_array($_SERVER['REQUEST_METHOD'], ['DELETE', 'POST'])) {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get held invoice ID
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        ApiResponse::error('Held invoice ID is required', 422);
    }
    
    $held_id = intval($input['id']);
    
    // Check if table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'held_invoices'");
    if (mysqli_num_rows($table_check) === 0) {
        ApiResponse::error('Held invoice not found', 404);
    }
    
    // Check if held invoice exists
    $check_query = "SELECT id, customer_name, status FROM held_invoices WHERE id = ?";
    $stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt, 'i', $held_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        ApiResponse::error('Held invoice not found', 404);
    }
    
    $invoice = mysqli_fetch_assoc($result);
    
    // Delete the held invoice (since 'cancelled' is not a valid status)
    $delete_query = "DELETE FROM held_invoices WHERE id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $held_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error deleting held invoice: " . mysqli_stmt_error($stmt));
    }
    
    // Return success
    ApiResponse::success('Held invoice deleted successfully', [
        'id' => $held_id,
        'customer_name' => $invoice['customer_name'],
        'previous_status' => $invoice['status'],
        'deleted_at' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to cancel held invoice', 500);
    }
}
