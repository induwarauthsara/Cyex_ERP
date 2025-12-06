<?php
/**
 * Update GRN API
 * 
 * Update an existing Goods Received Note
 * PUT /api/v1/grn/update.php
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

// Only allow PUT method
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        ApiResponse::error('Invalid JSON input', 400);
    }

    // Validate required fields
    if (!isset($input['grn_id']) || empty($input['grn_id'])) {
        ApiResponse::error('GRN ID is required', 400);
    }

    $grn_id = (int)$input['grn_id'];

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

    // Prepare update fields
    $update_fields = [];
    $update_params = [];
    $update_types = '';

    if (isset($input['invoice_number'])) {
        $update_fields[] = 'invoice_number = ?';
        $update_params[] = $input['invoice_number'];
        $update_types .= 's';
    }

    if (isset($input['invoice_date'])) {
        $update_fields[] = 'invoice_date = ?';
        $update_params[] = $input['invoice_date'] ?: null;
        $update_types .= 's';
    }

    if (isset($input['notes'])) {
        $update_fields[] = 'notes = ?';
        $update_params[] = $input['notes'];
        $update_types .= 's';
    }

    if (isset($input['payment_status']) && in_array($input['payment_status'], ['paid', 'partial', 'unpaid'])) {
        $update_fields[] = 'payment_status = ?';
        $update_params[] = $input['payment_status'];
        $update_types .= 's';
    }

    if (isset($input['paid_amount'])) {
        $update_fields[] = 'paid_amount = ?';
        $update_params[] = (float)$input['paid_amount'];
        $update_types .= 'd';
    }

    if (isset($input['payment_method'])) {
        $update_fields[] = 'payment_method = ?';
        $update_params[] = $input['payment_method'];
        $update_types .= 's';
    }

    if (isset($input['payment_reference'])) {
        $update_fields[] = 'payment_reference = ?';
        $update_params[] = $input['payment_reference'];
        $update_types .= 's';
    }

    if (empty($update_fields)) {
        ApiResponse::error('No fields to update', 400);
    }

    // Add updated timestamp
    $update_fields[] = 'updated_at = NOW()';

    // Build and execute update query
    $sql = "UPDATE goods_receipt_notes SET " . implode(', ', $update_fields) . " WHERE grn_id = ?";
    $update_params[] = $grn_id;
    $update_types .= 'i';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $update_types, ...$update_params);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update GRN: ' . mysqli_error($con));
    }

    // Get updated GRN
    $grn_sql = "
        SELECT 
            g.*,
            s.supplier_name,
            s.supplier_tel,
            s.supplier_address
        FROM goods_receipt_notes g
        LEFT JOIN suppliers s ON g.supplier_id = s.supplier_id
        WHERE g.grn_id = ?
    ";
    
    $grn_stmt = mysqli_prepare($con, $grn_sql);
    mysqli_stmt_bind_param($grn_stmt, 'i', $grn_id);
    mysqli_stmt_execute($grn_stmt);
    $grn_result = mysqli_stmt_get_result($grn_stmt);
    $grn = mysqli_fetch_assoc($grn_result);

    ApiResponse::success([
        'message' => 'GRN updated successfully',
        'grn' => [
            'grn_id' => (int)$grn['grn_id'],
            'grn_number' => $grn['grn_number'],
            'receipt_date' => $grn['receipt_date'],
            'invoice_number' => $grn['invoice_number'],
            'invoice_date' => $grn['invoice_date'],
            'total_amount' => (float)$grn['total_amount'],
            'paid_amount' => (float)$grn['paid_amount'],
            'payment_status' => $grn['payment_status'],
            'payment_method' => $grn['payment_method'],
            'payment_reference' => $grn['payment_reference'],
            'notes' => $grn['notes'],
            'status' => $grn['status'],
            'supplier' => [
                'supplier_id' => (int)$grn['supplier_id'],
                'supplier_name' => $grn['supplier_name'],
                'supplier_tel' => $grn['supplier_tel'],
                'supplier_address' => $grn['supplier_address']
            ],
            'created_at' => $grn['created_at'],
            'updated_at' => $grn['updated_at']
        ]
    ]);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
