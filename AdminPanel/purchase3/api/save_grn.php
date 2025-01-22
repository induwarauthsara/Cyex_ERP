<?php
// File: /AdminPanel/purchase/api/save_grn.php
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

        case 'validate_batches':
            handleValidateBatches($jsonData);
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
        // Generate GRN number
        $grnNumber = generateGRNNumber();

        // Insert main GRN record
        $query = "INSERT INTO goods_receipt_notes (
            grn_number, po_id, receipt_date, invoice_number,
            invoice_date, notes, status, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'sisssssi',
            $grnNumber,
            $data['po_id'],
            $data['receipt_date'],
            $data['invoice_number'],
            $data['invoice_date'] ?: null,
            $data['notes'],
            $data['status'],
            $_SESSION['employee_id']
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create GRN: ' . mysqli_error($con));
        }

        $grnId = mysqli_insert_id($con);

        // Create product batches and GRN items
        insertGRNItems($grnId, $data['items']);

        // If completing GRN, update PO status and received quantities
        if ($data['status'] === 'completed') {
            updatePurchaseOrder($data['po_id']);
        }

        // Log the transaction
        transaction_log(
            'CREATE_GRN',
            "Created GRN #$grnNumber",
            calculateTotalAmount($data['items'])
        );

        mysqli_commit($con);

        echo json_encode([
            'success' => true,
            'message' => 'GRN created successfully',
            'grn_id' => $grnId,
            'grn_number' => $grnNumber
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function handleUpdate($data) {
    global $con;

    if (!isset($data['grn_id'])) {
        throw new Exception('GRN ID is required');
    }

    validateFields($data);

    mysqli_begin_transaction($con);

    try {
        // Check if GRN exists and is in draft status
        $stmt = mysqli_prepare($con, 
            "SELECT status FROM goods_receipt_notes WHERE grn_id = ? FOR UPDATE"
        );
        mysqli_stmt_bind_param($stmt, 'i', $data['grn_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['status'] !== 'draft') {
                throw new Exception('Only draft GRNs can be updated');
            }
        } else {
            throw new Exception('GRN not found');
        }

        // Update main GRN record
        $query = "UPDATE goods_receipt_notes SET
            receipt_date = ?,
            invoice_number = ?,
            invoice_date = ?,
            notes = ?,
            status = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE grn_id = ?";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'sssssi',
            $data['receipt_date'],
            $data['invoice_number'],
            $data['invoice_date'] ?: null,
            $data['notes'],
            $data['status'],
            $data['grn_id']
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update GRN: ' . mysqli_error($con));
        }

        // Delete existing GRN items
        $query = "DELETE FROM grn_items WHERE grn_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $data['grn_id']);
        mysqli_stmt_execute($stmt);

        // Create new product batches and GRN items
        insertGRNItems($data['grn_id'], $data['items']);

        // If completing GRN, update PO status and received quantities
        if ($data['status'] === 'completed') {
            updatePurchaseOrder($data['po_id']);
        }

        // Log the transaction
        transaction_log(
            'UPDATE_GRN',
            "Updated GRN #{$data['grn_id']}",
            calculateTotalAmount($data['items'])
        );

        mysqli_commit($con);

        echo json_encode([
            'success' => true,
            'message' => 'GRN updated successfully'
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function handleValidateBatches($data) {
    global $con;

    if (empty($data['batches'])) {
        throw new Exception('No batches to validate');
    }

    $duplicates = [];
    foreach ($data['batches'] as $batch) {
        $query = "SELECT p.product_name, pb.batch_number
                 FROM product_batch pb
                 JOIN products p ON pb.product_id = p.product_id
                 WHERE pb.product_id = ? AND pb.batch_number = ?";
                 
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'is',
            $batch['product_id'],
            $batch['batch_number']
        );
        mysqli_stmt_execute($stmt);
        
        if ($row = mysqli_stmt_get_result($stmt)->fetch_assoc()) {
            $duplicates[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'duplicates' => $duplicates
    ]);
}

function validateFields($data) {
    if (empty($data['po_id'])) {
        throw new Exception('Purchase order ID is required');
    }

    if (empty($data['receipt_date'])) {
        throw new Exception('Receipt date is required');
    }

    if (empty($data['items']) || !is_array($data['items'])) {
        throw new Exception('At least one item is required');
    }

    foreach ($data['items'] as $item) {
        if (empty($item['product_id']) || 
            !isset($item['received_qty']) || 
            !isset($item['cost']) ||
            !isset($item['selling_price']) ||
            empty($item['batch_number'])) {
            throw new Exception('Invalid item data');
        }

        if ($item['received_qty'] <= 0) {
            throw new Exception('Received quantity must be greater than zero');
        }

        if ($item['cost'] <= 0) {
            throw new Exception('Cost must be greater than zero');
        }

        if ($item['selling_price'] <= 0) {
            throw new Exception('Selling price must be greater than zero');
        }
    }
}

function generateGRNNumber() {
    global $con;
    
    $query = "SELECT next_value, prefix, padding 
              FROM sequences 
              WHERE name = 'goods_receipt' 
              FOR UPDATE";
    
    $result = mysqli_query($con, $query);
    $seq = mysqli_fetch_assoc($result);
    
    if (!$seq) {
        throw new Exception('Failed to generate GRN number');
    }
    
    $nextNumber = str_pad($seq['next_value'], $seq['padding'], '0', STR_PAD_LEFT);
    $grnNumber = $seq['prefix'] . $nextNumber;
    
    $query = "UPDATE sequences 
              SET next_value = next_value + 1 
              WHERE name = 'goods_receipt'";

    if (!mysqli_query($con, $query)) {
        throw new Exception('Failed to update sequence number');
    }

    return $grnNumber;
}

function insertGRNItems($grnId, $items)
{
    global $con;

    foreach ($items as $item) {
        // Create product batch
        $query = "INSERT INTO product_batch (
            product_id, batch_number, cost, selling_price,
            expiry_date, quantity, purchase_date, status
        ) VALUES (?, ?, ?, ?, ?, ?, CURRENT_DATE, 'active')";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'isddsi',
            $item['product_id'],
            $item['batch_number'],
            $item['cost'],
            $item['selling_price'],
            $item['expiry_date'] ?: null,
            $item['received_qty']
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create product batch: ' . mysqli_error($con));
        }

        $batchId = mysqli_insert_id($con);

        // Create GRN item
        $query = "INSERT INTO grn_items (
            grn_id, product_id, batch_id, received_qty,
            cost, selling_price, expiry_date, notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'iiiiddss',
            $grnId,
            $item['product_id'],
            $batchId,
            $item['received_qty'],
            $item['cost'],
            $item['selling_price'],
            $item['expiry_date'] ?: null,
            $item['notes']
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create GRN item: ' . mysqli_error($con));
        }
    }
}

function updatePurchaseOrder($poId)
{
    global $con;

    // Get total ordered and received quantities
    $query = "SELECT 
                poi.po_item_id,
                poi.quantity as ordered_qty,
                COALESCE(SUM(gi.received_qty), 0) as total_received
              FROM purchase_order_items poi
              LEFT JOIN grn_items gi ON 
                gi.product_id = poi.product_id AND
                gi.grn_id IN (
                    SELECT grn_id 
                    FROM goods_receipt_notes 
                    WHERE po_id = ? AND status = 'completed'
                )
              WHERE poi.po_id = ?
              GROUP BY poi.po_item_id";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $poId, $poId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $allReceived = true;
    while ($row = mysqli_fetch_assoc($result)) {
        // Update received quantity for PO item
        $query = "UPDATE purchase_order_items 
                 SET received_qty = ?,
                     status = ?
                 WHERE po_item_id = ?";

        $status = $row['total_received'] >= $row['ordered_qty'] ? 'received' : 'pending';
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'isi',
            $row['total_received'],
            $status,
            $row['po_item_id']
        );
        mysqli_stmt_execute($stmt);

        if ($row['total_received'] < $row['ordered_qty']) {
            $allReceived = false;
        }
    }

    // Update PO status
    $status = $allReceived ? 'received' : 'ordered';
    $query = "UPDATE purchase_orders 
             SET status = ?,
                 updated_at = CURRENT_TIMESTAMP
             WHERE po_id = ?";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'si', $status, $poId);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update purchase order status: ' . mysqli_error($con));
    }
}

function calculateTotalAmount($items)
{
    $total = 0;
    foreach ($items as $item) {
        $total += $item['received_qty'] * $item['cost'];
    }
    return $total;
}