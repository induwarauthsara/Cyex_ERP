<?php
// File: /AdminPanel/purchase/api/save_po.php
require_once '../../../inc/config.php';

// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

try {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    $action = $jsonData['action'] ?? '';

    switch ($action) {
        case 'create':
            handleCreate($jsonData);
            break;

        case 'update':
            handleUpdate($jsonData);
            break;

        case 'get':
            handleGet($_GET['id'] ?? null);
            break;

        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleCreate($data) {
    global $con;

    // Validate required fields
    validateFields($data);

    mysqli_begin_transaction($con);

    try {
        // Generate PO number
        $poNumber = generatePoNumber();

        // Insert main PO record
        $query = "INSERT INTO purchase_orders (
            po_number, supplier_id, order_date, delivery_date,
            shipping_fee, discount_type, discount_value,
            status, notes, created_by, subtotal, total_amount
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $subtotal = calculateSubtotal($data['items']);
        $totalAmount = calculateTotal($subtotal, $data['discount_type'], 
                                    $data['discount_value'], $data['shipping_fee']);

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'sissdssssidd',
            $poNumber,
            $data['supplier_id'],
            $data['order_date'],
            $data['delivery_date'] ?: null,
            $data['shipping_fee'],
            $data['discount_type'],
            $data['discount_value'],
            $data['status'],
            $data['notes'],
            $_SESSION['employee_id'],
            $subtotal,
            $totalAmount
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create purchase order: ' . mysqli_error($con));
        }

        $poId = mysqli_insert_id($con);

        // Insert PO items
        insertPoItems($poId, $data['items']);

        // Log the transaction
        transaction_log(
            'CREATE_PO',
            "Created purchase order #$poNumber",
            $totalAmount
        );

        mysqli_commit($con);

        echo json_encode([
            'success' => true,
            'message' => 'Purchase order created successfully',
            'po_id' => $poId,
            'po_number' => $poNumber
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function handleUpdate($data) {
    global $con;

    if (!isset($data['po_id'])) {
        throw new Exception('Purchase order ID is required');
    }

    validateFields($data);

    mysqli_begin_transaction($con);

    try {
        // Check if PO exists and can be edited
        $stmt = mysqli_prepare($con, 
            "SELECT status FROM purchase_orders WHERE po_id = ? FOR UPDATE"
        );
        mysqli_stmt_bind_param($stmt, 'i', $data['po_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (!in_array($row['status'], ['draft', 'pending'])) {
                throw new Exception('This purchase order cannot be edited');
            }
        } else {
            throw new Exception('Purchase order not found');
        }

        // Update main PO record
        $subtotal = calculateSubtotal($data['items']);
        $totalAmount = calculateTotal($subtotal, $data['discount_type'], 
                                    $data['discount_value'], $data['shipping_fee']);

        $query = "UPDATE purchase_orders SET 
            order_date = ?,
            delivery_date = ?,
            shipping_fee = ?,
            discount_type = ?,
            discount_value = ?,
            status = ?,
            notes = ?,
            subtotal = ?,
            total_amount = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE po_id = ?";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'ssdsssdddi',
            $data['order_date'],
            $data['delivery_date'] ?: null,
            $data['shipping_fee'],
            $data['discount_type'],
            $data['discount_value'],
            $data['status'],
            $data['notes'],
            $subtotal,
            $totalAmount,
            $data['po_id']
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update purchase order: ' . mysqli_error($con));
        }

        // Delete existing items
        $query = "DELETE FROM purchase_order_items WHERE po_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $data['po_id']);
        mysqli_stmt_execute($stmt);

        // Insert updated items
        insertPoItems($data['po_id'], $data['items']);

        // Log the transaction
        transaction_log(
            'UPDATE_PO',
            "Updated purchase order #{$data['po_id']}",
            $totalAmount
        );

        mysqli_commit($con);

        echo json_encode([
            'success' => true,
            'message' => 'Purchase order updated successfully'
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function handleGet($poId) {
    global $con;

    if (!$poId) {
        throw new Exception('Purchase order ID is required');
    }

    // Get PO details
    $query = "SELECT po.*, s.supplier_name 
              FROM purchase_orders po
              JOIN suppliers s ON po.supplier_id = s.supplier_id
              WHERE po_id = ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $poId);
    mysqli_stmt_execute($stmt);
    $po = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$po) {
        throw new Exception('Purchase order not found');
    }

    // Get PO items
    $query = "SELECT poi.*, p.product_name
              FROM purchase_order_items poi
              JOIN products p ON poi.product_id = p.product_id
              WHERE po_id = ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $poId);
    mysqli_stmt_execute($stmt);
    $items = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'po' => $po,
        'items' => $items
    ]);
}

function validateFields($data) {
    if (empty($data['supplier_id'])) {
        throw new Exception('Supplier is required');
    }

    if (empty($data['order_date'])) {
        throw new Exception('Order date is required');
    }

    if (empty($data['items']) || !is_array($data['items'])) {
        throw new Exception('At least one product is required');
    }

    foreach ($data['items'] as $item) {
        if (empty($item['product_id']) || 
            empty($item['quantity']) || 
            !isset($item['unit_cost'])) {
            throw new Exception('Invalid product data');
        }
    }
}

function generatePoNumber()
{
    global $con;

    $query = "SELECT next_value, prefix, padding 
              FROM sequences 
              WHERE name = 'purchase_order' 
              FOR UPDATE";

    $result = mysqli_query($con, $query);
    $seq = mysqli_fetch_assoc($result);

    if (!$seq) {
        throw new Exception('Failed to generate PO number');
    }

    $nextNumber = str_pad($seq['next_value'], $seq['padding'], '0', STR_PAD_LEFT);
    $poNumber = $seq['prefix'] . $nextNumber;

    // Update sequence
    $query = "UPDATE sequences 
              SET next_value = next_value + 1 
              WHERE name = 'purchase_order'";

    if (!mysqli_query($con, $query)) {
        throw new Exception('Failed to update sequence number');
    }

    return $poNumber;
}

function insertPoItems($poId, $items)
{
    global $con;

    $query = "INSERT INTO purchase_order_items (
                po_id, product_id, quantity, unit_cost, total_cost, status
              ) VALUES (?, ?, ?, ?, ?, 'pending')";

    $stmt = mysqli_prepare($con, $query);

    foreach ($items as $item) {
        $totalCost = $item['quantity'] * $item['unit_cost'];

        mysqli_stmt_bind_param(
            $stmt,
            'iiiddd',
            $poId,
            $item['product_id'],
            $item['quantity'],
            $item['unit_cost'],
            $totalCost
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to insert purchase order item: ' . mysqli_error($con));
        }
    }
}

function calculateSubtotal($items)
{
    return array_reduce($items, function ($sum, $item) {
        return $sum + ($item['quantity'] * $item['unit_cost']);
    }, 0);
}

function calculateTotal($subtotal, $discountType, $discountValue, $shippingFee)
{
    $discount = $discountType === 'percentage'
        ? $subtotal * ($discountValue / 100)
        : $discountValue;

    return $subtotal - $discount + $shippingFee;
}