<?php
ob_start();
require_once('../nav.php');
ob_clean();

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
    $required_fields = ['supplier_id', 'order_date', 'status', 'items'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate items
    if (!is_array($input['items']) || empty($input['items'])) {
        throw new Exception('At least one item is required');
    }

    foreach ($input['items'] as $item) {
        if (!isset($item['product_id'], $item['quantity'], $item['unit_cost'])) {
            throw new Exception('Invalid item data');
        }
        if ($item['quantity'] <= 0 || $item['unit_cost'] <= 0) {
            throw new Exception('Invalid quantity or unit cost');
        }
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Generate PO number
    $po_number = generatePONumber($con);

    // Calculate totals
    $subtotal = 0;
    foreach ($input['items'] as $item) {
        $subtotal += $item['quantity'] * $item['unit_cost'];
    }

    $shipping_fee = floatval($input['shipping_fee'] ?? 0);

    // Calculate discount
    $discount_value = floatval($input['discount_value'] ?? 0);
    $discount_type = $input['discount_type'] ?? 'fixed';
    $total_discount = $discount_type === 'percentage' ?
        ($subtotal * $discount_value / 100) : $discount_value;

    // Calculate tax
    $tax_value = floatval($input['tax_value'] ?? 0);
    $tax_type = $input['tax_type'] ?? 'percentage';
    $total_tax = $tax_type === 'percentage' ?
        (($subtotal - $total_discount) * $tax_value / 100) : $tax_value;

    // Calculate total amount
    $total_amount = $subtotal - $total_discount + $total_tax + $shipping_fee;

    // Insert purchase order
    $po_sql = "
        INSERT INTO purchase_orders (
            po_number, supplier_id, order_date, delivery_date,
            shipping_fee, discount_type, discount_value, tax_type,
            tax_value, status, subtotal, total_discount, total_tax,
            total_amount, notes, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare($con, $po_sql);
    mysqli_stmt_bind_param(
        $stmt,
        'sissdsdsdssddds',
        $po_number,
        $input['supplier_id'],
        $input['order_date'],
        $input['delivery_date'] ?? null,
        $shipping_fee,
        $discount_type,
        $discount_value,
        $tax_type,
        $tax_value,
        $input['status'],
        $subtotal,
        $total_discount,
        $total_tax,
        $total_amount,
        $input['notes'] ?? null,
        $_SESSION['employee_id']
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to create purchase order');
    }

    $po_id = mysqli_insert_id($con);

    // Insert purchase order items
    $items_sql = "
        INSERT INTO purchase_order_items (
            po_id, product_id, quantity, unit_cost, total_cost
        ) VALUES (?, ?, ?, ?, ?)
    ";

    $items_stmt = mysqli_prepare($con, $items_sql);
    foreach ($input['items'] as $item) {
        $total_cost = $item['quantity'] * $item['unit_cost'];
        mysqli_stmt_bind_param(
            $items_stmt,
            'iidd',
            $po_id,
            $item['product_id'],
            $item['quantity'],
            $item['unit_cost'],
            $total_cost
        );

        if (!mysqli_stmt_execute($items_stmt)) {
            throw new Exception('Failed to add purchase order item');
        }
    }

    // Log transaction
    transaction_log(
        'CREATE_PO',
        "Created Purchase Order #$po_number",
        $total_amount
    );

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Purchase order created successfully',
        'po_id' => $po_id,
        'po_number' => $po_number
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

/**
 * Generate a unique PO number
 */
function generatePONumber($con)
{
    // Get next sequence value
    $seq_sql = "
        INSERT INTO sequences (name, prefix, next_value, padding)
        VALUES ('po_number', 'PO', 1, 5)
        ON DUPLICATE KEY UPDATE next_value = next_value + 1
    ";

    if (!insert_query($seq_sql, "Updated PO sequence", "Update Sequence")) {
        throw new Exception('Failed to generate PO number');
    }

    $get_seq_sql = "SELECT next_value - 1 as value, prefix, padding 
                    FROM sequences WHERE name = 'po_number'";
    $seq_result = mysqli_query($con, $get_seq_sql);
    $seq_data = mysqli_fetch_assoc($seq_result);

    return $seq_data['prefix'] . str_pad(
        $seq_data['value'],
        $seq_data['padding'],
        '0',
        STR_PAD_LEFT
    );
}
ob_end_flush();