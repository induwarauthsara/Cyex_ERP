<?php
require_once('../../../inc/config.php');

header('Content-Type: application/json');

if (!isset($_POST['supplier_id'], $_POST['items'], $_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $items = json_decode($_POST['items'], true);
    if (empty($items)) {
        throw new Exception('No items provided');
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Calculate totals
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['quantity'] * $item['unit_cost'];
    }

    $shipping_fee = floatval($_POST['shipping_fee'] ?? 0);
    $discount_type = $_POST['discount_type'] ?? 'fixed';
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $tax_value = floatval($_POST['tax_value'] ?? 0);

    // Calculate discount
    $total_discount = $discount_type === 'percentage'
        ? $subtotal * ($discount_value / 100)
        : $discount_value;

    // Calculate tax
    $total_tax = ($subtotal - $total_discount) * ($tax_value / 100);

    // Calculate total amount
    $total_amount = $subtotal - $total_discount + $total_tax + $shipping_fee;

    // Insert PO
    $query = "INSERT INTO purchase_orders (
                po_number,
                supplier_id,
                order_date,
                delivery_date,
                shipping_fee,
                discount_type,
                discount_value,
                tax_value,
                status,
                subtotal,
                total_discount,
                total_tax,
                total_amount,
                notes,
                created_by
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "sissdsddsddddsi",
        $_POST['po_number'],
        $_POST['supplier_id'],
        $_POST['order_date'],
        $_POST['delivery_date'],
        $shipping_fee,
        $discount_type,
        $discount_value,
        $tax_value,
        $_POST['status'],
        $subtotal,
        $total_discount,
        $total_tax,
        $total_amount,
        $_POST['notes'],
        $_SESSION['employee_id']
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error creating purchase order');
    }

    $po_id = mysqli_insert_id($con);

    // Insert PO items
    $query = "INSERT INTO purchase_order_items (
                po_id,
                product_id,
                quantity,
                unit_cost,
                total_cost
              ) VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $query);
    foreach ($items as $item) {
        $total = $item['quantity'] * $item['unit_cost'];
        mysqli_stmt_bind_param(
            $stmt,
            "iiddd",
            $po_id,
            $item['product_id'],
            $item['quantity'],
            $item['unit_cost'],
            $total
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error adding purchase order items');
        }
    }

    // Update sequence
    mysqli_query($con, "UPDATE sequences SET next_value = next_value + 1 WHERE name = 'purchase_order'");

    // Log the action
    $action = "Create PO";
    $msg = "Created purchase order #{$_POST['po_number']} with " . count($items) . " items";
    insert_query("SELECT 1", $msg, $action);

    // Log the transaction
    transaction_log(
        'Purchase Order',
        "Created PO #{$_POST['po_number']}",
        $total_amount
    );

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Purchase order created successfully',
        'po_id' => $po_id
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
