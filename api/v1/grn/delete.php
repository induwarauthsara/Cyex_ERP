<?php
/**
 * Delete GRN API
 * 
 * Delete (mark as cancelled) a Goods Received Note
 * DELETE /api/v1/grn/delete.php
 * 
 * Admin only
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate and require admin role
$user = ApiAuth::requireAuth();
if ($user['employee_role'] !== 'Admin') {
    ApiResponse::forbidden('Admin access required');
}

// Only allow DELETE method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // Try to get from query string
        $grn_id = isset($_GET['grn_id']) ? (int)$_GET['grn_id'] : null;
    } else {
        $grn_id = isset($input['grn_id']) ? (int)$input['grn_id'] : null;
    }

    if (!$grn_id) {
        ApiResponse::error('GRN ID is required', 400);
    }

    // Check if GRN exists
    $check_sql = "SELECT grn_id, status FROM goods_receipt_notes WHERE grn_id = ?";
    $check_stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 'i', $grn_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) === 0) {
        ApiResponse::error('GRN not found', 404);
    }

    $grn_data = mysqli_fetch_assoc($check_result);

    if ($grn_data['status'] === 'cancelled') {
        ApiResponse::error('GRN is already cancelled', 400);
    }

    // Begin transaction
    mysqli_begin_transaction($con);

    try {
        // Mark GRN as cancelled
        $update_sql = "UPDATE goods_receipt_notes SET status = 'cancelled', updated_at = NOW() WHERE grn_id = ?";
        $update_stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($update_stmt, 'i', $grn_id);
        
        if (!mysqli_stmt_execute($update_stmt)) {
            throw new Exception('Failed to cancel GRN: ' . mysqli_error($con));
        }

        // Get GRN items to reverse stock
        $items_sql = "SELECT gi.batch_id, gi.received_qty, pb.product_id 
                      FROM grn_items gi
                      LEFT JOIN product_batch pb ON gi.batch_id = pb.batch_id
                      WHERE gi.grn_id = ?";
        $items_stmt = mysqli_prepare($con, $items_sql);
        mysqli_stmt_bind_param($items_stmt, 'i', $grn_id);
        mysqli_stmt_execute($items_stmt);
        $items_result = mysqli_stmt_get_result($items_stmt);

        // Reverse stock quantities
        while ($item = mysqli_fetch_assoc($items_result)) {
            if ($item['batch_id']) {
                $reverse_sql = "UPDATE product_batch SET quantity = quantity - ? WHERE batch_id = ?";
                $reverse_stmt = mysqli_prepare($con, $reverse_sql);
                mysqli_stmt_bind_param($reverse_stmt, 'di', $item['received_qty'], $item['batch_id']);
                
                if (!mysqli_stmt_execute($reverse_stmt)) {
                    throw new Exception('Failed to reverse stock for batch: ' . mysqli_error($con));
                }
            }
        }

        // Commit transaction
        mysqli_commit($con);

        ApiResponse::success([
            'message' => 'GRN cancelled successfully',
            'grn_id' => $grn_id
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
