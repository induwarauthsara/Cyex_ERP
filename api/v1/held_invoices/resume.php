<?php
/**
 * Resume Held Invoice API
 * 
 * Retrieve a held invoice for processing
 * GET /api/v1/held_invoices/resume.php?id=123
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate user
$user = ApiAuth::requireAuth();

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get held invoice ID
    if (!isset($_GET['id'])) {
        ApiResponse::error('Held invoice ID is required', 422);
    }
    
    $held_id = intval($_GET['id']);
    
    // Check if table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'held_invoices'");
    if (mysqli_num_rows($table_check) === 0) {
        ApiResponse::error('Held invoice not found', 404);
    }
    
    // Get held invoice
    $query = "SELECT 
                h.id,
                h.customer_name,
                h.customer_number,
                h.items,
                h.total_amount,
                h.discount_amount,
                h.discount_type,
                h.discount_value,
                h.total_payable,
                h.held_at,
                h.individual_discount_mode
              FROM held_invoices h
              WHERE h.id = ? AND h.status = 'held'";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $held_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        ApiResponse::error('Held invoice not found or already processed', 404);
    }
    
    $row = mysqli_fetch_assoc($result);
    $items = json_decode($row['items'], true);
    
    // Return invoice data in format ready for POS
    ApiResponse::success('Held invoice retrieved successfully', [
        'id' => (int)$row['id'],
        'customerName' => $row['customer_name'],
        'customerNumber' => $row['customer_number'],
        'items' => $items,
        'totalAmount' => (float)$row['total_amount'],
        'discountAmount' => (float)$row['discount_amount'],
        'discountType' => $row['discount_type'],
        'discountValue' => (float)$row['discount_value'],
        'totalPayable' => (float)$row['total_payable'],
        'heldAt' => $row['held_at'],
        'individualDiscountMode' => (bool)$row['individual_discount_mode']
    ]);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve held invoice', 500);
    }
}
