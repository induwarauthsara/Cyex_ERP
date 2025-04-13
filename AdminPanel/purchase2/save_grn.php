<?php
require_once '../../inc/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $required_fields = ['supplier_id', 'receipt_date', 'items'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Generate GRN number
    $grn_number = generateGRNNumber($con);

    // Insert GRN header
    $grn_sql = "
        INSERT INTO goods_receipt_notes (
            grn_number, po_id, receipt_date, invoice_number,
            invoice_date, notes, status, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare($con, $grn_sql);
    mysqli_stmt_bind_param(
        $stmt,
        'sissssi',
        $grn_number,
        $input['po_id'],
        $input['receipt_date'],
        $input['invoice_number'],
        $input['invoice_date'],
        $input['notes'],
        $input['status'],
        $_SESSION['employee_id']
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to create GRN');
    }

    $grn_id = mysqli_insert_id($con);

    // Process items
    foreach ($input['items'] as $item) {
        // Validate batch number uniqueness
        $check_batch_sql = "
            SELECT COUNT(*) as count 
            FROM product_batch 
            WHERE product_id = ? AND batch_number = ?
        ";
        $check_stmt = mysqli_prepare($con, $check_batch_sql);
        mysqli_stmt_bind_param($check_stmt, 'is', $item['product_id'], $item['batch_number']);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $batch_exists = mysqli_fetch_assoc($check_result)['count'] > 0;

        // Create or update batch
        if ($batch_exists) {
            // Update existing batch
            $batch_sql = "
                UPDATE product_batch 
                SET quantity = quantity + ?,
                    cost = ?,
                    selling_price = ?,
                    expiry_date = ?,
                    restocked_at = NOW()
                WHERE product_id = ? AND batch_number = ?
            ";
            
            if (!insert_query(
                $batch_sql,
                "Updated batch {$item['batch_number']} for product #{$item['product_id']}",
                "Update Batch"
            )) {
                throw new Exception('Failed to update batch');
            }

            // Get batch ID
            $get_batch_sql = "
                SELECT batch_id 
                FROM product_batch 
                WHERE product_id = ? AND batch_number = ?
            ";
            $get_batch_stmt = mysqli_prepare($con, $get_batch_sql);
            mysqli_stmt_bind_param($get_batch_stmt, 'is', $item['product_id'], $item['batch_number']);
            mysqli_stmt_execute($get_batch_stmt);
            $batch_result = mysqli_stmt_get_result($get_batch_stmt);
            $batch_id = mysqli_fetch_assoc($batch_result)['batch_id'];
        } else {
            // Create new batch
            $batch_sql = "
                INSERT INTO product_batch (
                    product_id, batch_number, cost, selling_price,
                    expiry_date, quantity, supplier_id, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
            ";
            
            if (!insert_query(
                $batch_sql,
                "Created new batch {$item['batch_number']} for product #{$item['product_id']}",
                "Create Batch"
            )) {
                throw new Exception('Failed to create batch');
            }

            $batch_id = mysqli_insert_id($con);
        }

        // Insert GRN item
        $grn_item_sql = "
            INSERT INTO grn_items (
                grn_id, po_item_id, batch_id, received_qty,
                cost, selling_price, expiry_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        