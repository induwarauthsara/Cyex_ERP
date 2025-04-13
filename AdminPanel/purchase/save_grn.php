<?php
require_once('../../../inc/config.php');

header('Content-Type: application/json');

if (!isset($_POST['po_id'], $_POST['processed_items'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Decode processed items
    $processedItems = json_decode($_POST['processed_items'], true);
    if (empty($processedItems)) {
        throw new Exception('No items provided');
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Insert GRN
    $query = "INSERT INTO goods_receipt_notes (
                grn_number,
                po_id,
                receipt_date,
                invoice_number,
                invoice_date,
                notes,
                status,
                created_by
              ) VALUES (?, ?, ?, ?, ?, ?, 'completed', ?)";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "sissssi",
        $_POST['grn_number'],
        $_POST['po_id'],
        $_POST['receipt_date'],
        $_POST['invoice_number'],
        $_POST['invoice_date'],
        $_POST['notes'],
        $_SESSION['employee_id']
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error creating GRN');
    }

    $grn_id = mysqli_insert_id($con);

    // Process each item
    foreach ($processedItems as $poItemId => $item) {
        $row = $item;

        // Create or update batch
        if ($row['batch_option'] === 'new') {
            // Create new batch
            $batch_query = "INSERT INTO product_batch (
                            product_id,
                            batch_number,
                            cost,
                            selling_price,
                            expiry_date,
                            quantity,
                            supplier_id,
                            notes,
                            status
                          ) SELECT 
                            poi.product_id,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            po.supplier_id,
                            ?,
                            'active'
                          FROM purchase_order_items poi
                          JOIN purchase_orders po ON poi.po_id = po.po_id
                          WHERE poi.po_item_id = ?";

            $stmt = mysqli_prepare($con, $batch_query);
            mysqli_stmt_bind_param(
                $stmt,
                "sdddssi",
                $row['batch_number'],
                $row['cost'],
                $row['selling_price'],
                $row['expiry_date'],
                $_POST['receiving_qty'][$poItemId],
                $row['notes'],
                $poItemId
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Error creating batch');
            }

            $batch_id = mysqli_insert_id($con);
        } else {
            // Update existing batch
            $batch_id = $row['batch_id'];

            $update_query = "UPDATE product_batch 
                           SET quantity = quantity + ?,
                               cost = ?,
                               selling_price = ?,
                               notes = CONCAT(notes, '\nUpdated via GRN #', ?)
                           WHERE batch_id = ?";

            $stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param(
                $stmt,
                "dddsi",
                $_POST['receiving_qty'][$poItemId],
                $row['cost'],
                $row['selling_price'],
                $_POST['grn_number'],
                $batch_id
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Error updating batch');
            }
        }

        // Insert GRN item
        $grn_item_query = "INSERT INTO grn_items (
                            grn_id,
                            po_item_id,
                            batch_id,
                            received_qty,
                            cost,
                            selling_price,
                            expiry_date,
                            notes
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $grn_item_query);
        mysqli_stmt_bind_param(
            $stmt,
            "iiidddsss",
            $grn_id,
            $poItemId,
            $batch_id,
            $_POST['receiving_qty'][$poItemId],
            $row['cost'],
            $row['selling_price'],
            $row['expiry_date'],
            $row['notes']
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error creating GRN item');
        }

        // Update product barcode if changed
        if (!empty($row['barcode'])) {
            $barcode_query = "UPDATE products p
                             JOIN purchase_order_items poi ON p.product_id = poi.product_id
                             SET p.barcode = ?
                             WHERE poi.po_item_id = ?";

            $stmt = mysqli_prepare($con, $barcode_query);
            mysqli_stmt_bind_param($stmt, "si", $row['barcode'], $poItemId);
            mysqli_stmt_execute($stmt);
        }
    }

    // Update PO status if all items received
    $check_query = "SELECT 
                     COUNT(*) as total_items,
                     SUM(CASE WHEN quantity <= received_qty THEN 1 ELSE 0 END) as received_items
                   FROM purchase_order_items
                   WHERE po_id = ?";

    $stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $_POST['po_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $counts = mysqli_fetch_assoc($result);

    if ($counts['total_items'] == $counts['received_items']) {
        $status_query = "UPDATE purchase_orders SET status = 'received' WHERE po_id = ?";
        $stmt = mysqli_prepare($con, $status_query);
        mysqli_stmt_bind_param($stmt, "i", $_POST['po_id']);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error updating PO status');
        }
    }

    // Update sequence
    mysqli_query($con, "UPDATE sequences SET next_value = next_value + 1 WHERE name = 'grn'");

    // Log the action
    $msg = "Created GRN #{$_POST['grn_number']} for PO #{$_POST['po_number']}";
    $action = "Create GRN";
    insert_query("SELECT 1", $msg, $action);

    // Get total value for transaction log
    $value_query = "SELECT SUM(received_qty * cost) as total
                   FROM grn_items
                   WHERE grn_id = ?";
    $stmt = mysqli_prepare($con, $value_query);
    mysqli_stmt_bind_param($stmt, "i", $grn_id);
    mysqli_stmt_execute($stmt);
    $total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

    // Log the transaction
    transaction_log(
        'GRN Created',
        "GRN #{$_POST['grn_number']} for PO #{$_POST['po_number']}",
        $total
    );

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'GRN created successfully',
        'po_id' => $_POST['po_id'],
        'grn_id' => $grn_id
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
