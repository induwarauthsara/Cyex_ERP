<?php
require_once '../../inc/config.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'create_po':
                mysqli_begin_transaction($con);
                try {
                    // Get next PO number
                    $result = mysqli_query($con, "SELECT get_next_number('purchase_order') as po_number");
                    $row = mysqli_fetch_assoc($result);
                    $poNumber = $row['po_number'];

                    // Parse and validate input data
                    $supplierId = (int)$_POST['supplier_id'];
                    $deliveryDate = mysqli_real_escape_string($con, $_POST['delivery_date']);
                    $shippingFee = (float)$_POST['shipping_fee'];
                    $discountType = mysqli_real_escape_string($con, $_POST['discount_type']);
                    $discountValue = (float)$_POST['discount_value'];
                    $taxType = mysqli_real_escape_string($con, $_POST['tax_type']);
                    $taxValue = (float)$_POST['tax_value'];
                    $notes = mysqli_real_escape_string($con, $_POST['notes']);
                    $items = json_decode($_POST['items'], true);

                    if (empty($items)) {
                        throw new Exception("No items provided");
                    }

                    // Calculate totals
                    $subtotal = 0;
                    foreach ($items as $item) {
                        $subtotal += $item['quantity'] * $item['unit_cost'];
                    }

                    // Calculate discount
                    $totalDiscount = $discountType === 'percentage'
                        ? ($subtotal * $discountValue / 100)
                        : $discountValue;

                    // Calculate tax
                    $totalTax = $taxType === 'percentage'
                        ? (($subtotal - $totalDiscount) * $taxValue / 100)
                        : $taxValue;

                    // Calculate final total
                    $totalAmount = $subtotal - $totalDiscount + $totalTax + $shippingFee;

                    // Insert PO header
                    $query = "INSERT INTO purchase_orders (
                        po_number, supplier_id, delivery_date, shipping_fee,
                        discount_type, discount_value, tax_type, tax_value,
                        subtotal, total_discount, total_tax, total_amount,
                        notes, created_by, status
                    ) VALUES (
                        '$poNumber', $supplierId, '$deliveryDate', $shippingFee,
                        '$discountType', $discountValue, '$taxType', $taxValue,
                        $subtotal, $totalDiscount, $totalTax, $totalAmount,
                        '$notes', $employee_id, 'draft'
                    )";

                    if (!insert_query($query, "Created PO: $poNumber", "CREATE_PO")) {
                        throw new Exception("Failed to create purchase order : $query");
                    }

                    $poId = mysqli_insert_id($con);

                    // Insert PO items
                    foreach ($items as $item) {
                        $productId = (int)$item['product_id'];
                        $quantity = (int)$item['quantity'];
                        $unitCost = (float)$item['unit_cost'];
                        $totalCost = $quantity * $unitCost;

                        $query = "INSERT INTO purchase_order_items (
                            po_id, product_id, quantity, unit_cost, total_cost
                        ) VALUES (
                            $poId, $productId, $quantity, $unitCost, $totalCost
                        )";

                        if (!insert_query($query, "Added PO item: $productId", "CREATE_PO_ITEM")) {
                            throw new Exception("Failed to add purchase order item");
                        }
                    }

                    mysqli_commit($con);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Purchase order created successfully',
                        'po_number' => $poNumber
                    ]);
                } catch (Exception $e) {
                    mysqli_rollback($con);
                    throw $e;
                }
                break;


            case 'update_po':
                $poId = (int)$_POST['po_id'];
                mysqli_begin_transaction($con);

                try {
                    // Validate PO exists and is in draft status
                    $checkQuery = "SELECT status FROM purchase_orders WHERE po_id = $poId";
                    $result = mysqli_query($con, $checkQuery);
                    $po = mysqli_fetch_assoc($result);

                    if (!$po) {
                        throw new Exception("Purchase order not found");
                    }
                    if ($po['status'] !== 'draft') {
                        throw new Exception("Only draft purchase orders can be edited");
                    }

                    // Parse input data
                    $supplierId = (int)$_POST['supplier_id'];
                    $deliveryDate = mysqli_real_escape_string($con, $_POST['delivery_date']);
                    $shippingFee = (float)$_POST['shipping_fee'];
                    $discountType = mysqli_real_escape_string($con, $_POST['discount_type']);
                    $discountValue = (float)$_POST['discount_value'];
                    $taxType = mysqli_real_escape_string($con, $_POST['tax_type']);
                    $taxValue = (float)$_POST['tax_value'];
                    $notes = mysqli_real_escape_string($con, $_POST['notes']);
                    $items = json_decode($_POST['items'], true);

                    if (empty($items)) {
                        throw new Exception("No items provided");
                    }

                    // Calculate totals (similar to create_po)
                    $subtotal = 0;
                    foreach ($items as $item) {
                        $subtotal += $item['quantity'] * $item['unit_cost'];
                    }

                    $totalDiscount = $discountType === 'percentage'
                        ? ($subtotal * $discountValue / 100)
                        : $discountValue;

                    $totalTax = $taxType === 'percentage'
                        ? (($subtotal - $totalDiscount) * $taxValue / 100)
                        : $taxValue;

                    $totalAmount = $subtotal - $totalDiscount + $totalTax + $shippingFee;

                    // Update PO header
                    $query = "UPDATE purchase_orders SET 
                        supplier_id = $supplierId,
                        delivery_date = '$deliveryDate',
                        shipping_fee = $shippingFee,
                        discount_type = '$discountType',
                        discount_value = $discountValue,
                        tax_type = '$taxType',
                        tax_value = $taxValue,
                        subtotal = $subtotal,
                        total_discount = $totalDiscount,
                        total_tax = $totalTax,
                        total_amount = $totalAmount,
                        notes = '$notes'
                        WHERE po_id = $poId";

                    if (!insert_query($query, "Updated PO: $poId", "UPDATE_PO")) {
                        throw new Exception("Failed to update purchase order");
                    }

                    // Delete existing items
                    $query = "DELETE FROM purchase_order_items WHERE po_id = $poId";
                    if (!insert_query($query, "Deleted old PO items", "DELETE_PO_ITEMS")) {
                        throw new Exception("Failed to update items");
                    }

                    // Insert updated items
                    foreach ($items as $item) {
                        $productId = (int)$item['product_id'];
                        $quantity = (int)$item['quantity'];
                        $unitCost = (float)$item['unit_cost'];
                        $totalCost = $quantity * $unitCost;

                        $query = "INSERT INTO purchase_order_items (
                            po_id, product_id, quantity, unit_cost, total_cost
                        ) VALUES (
                            $poId, $productId, $quantity, $unitCost, $totalCost
                        )";

                        if (!insert_query($query, "Added updated PO item: $productId", "UPDATE_PO_ITEM")) {
                            throw new Exception("Failed to update purchase order item");
                        }
                    }

                    mysqli_commit($con);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Purchase order updated successfully'
                    ]);
                } catch (Exception $e) {
                    mysqli_rollback($con);
                    throw $e;
                }
                break;
                $poId = (int)$_POST['po_id'];
                $status = mysqli_real_escape_string($con, $_POST['status']);
                $itemStatuses = json_decode($_POST['item_statuses'] ?? '[]', true);

                mysqli_begin_transaction($con);
                try {
                    // Update PO status
                    $query = "UPDATE purchase_orders SET status = '$status' WHERE po_id = $poId";
                    if (!insert_query($query, "Updated PO status: $status", "UPDATE_PO_STATUS")) {
                        throw new Exception("Failed to update purchase order status");
                    }

                    // Update item statuses if provided
                    if (!empty($itemStatuses)) {
                        foreach ($itemStatuses as $item) {
                            $itemId = (int)$item['po_item_id'];
                            $itemStatus = mysqli_real_escape_string($con, $item['status']);

                            $query = "UPDATE purchase_order_items 
                                     SET status = '$itemStatus'
                                     WHERE po_item_id = $itemId";

                            if (!insert_query($query, "Updated PO item status: $itemStatus", "UPDATE_PO_ITEM_STATUS")) {
                                throw new Exception("Failed to update item status");
                            }
                        }
                    }

                    mysqli_commit($con);
                    echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
                } catch (Exception $e) {
                    mysqli_rollback($con);
                    throw $e;
                }
                break;

            case 'create_grn':
                mysqli_begin_transaction($con);
                try {
                    // Get next GRN number
                    $result = mysqli_query($con, "SELECT get_next_number('goods_receipt') as grn_number");
                    $row = mysqli_fetch_assoc($result);
                    $grnNumber = $row['grn_number'];

                    $poId = (int)$_POST['po_id'];
                    $receiptDate = mysqli_real_escape_string($con, $_POST['receipt_date']);
                    $invoiceNumber = mysqli_real_escape_string($con, $_POST['invoice_number']);
                    $invoiceDate = mysqli_real_escape_string($con, $_POST['invoice_date']);
                    $notes = mysqli_real_escape_string($con, $_POST['notes']);
                    $items = json_decode($_POST['items'], true);

                    if (empty($items)) {
                        throw new Exception("No items provided");
                    }

                    // Insert GRN header
                    $query = "INSERT INTO goods_receipt_notes (
                        grn_number, po_id, receipt_date, invoice_number,
                        invoice_date, notes, created_by, status
                    ) VALUES (
                        '$grnNumber', $poId, '$receiptDate', '$invoiceNumber',
                        '$invoiceDate', '$notes', $employee_id, 'draft'
                    )";

                    if (!insert_query($query, "Created GRN: $grnNumber", "CREATE_GRN")) {
                        throw new Exception("Failed to create GRN");
                    }

                    $grnId = mysqli_insert_id($con);

                    // Process each item
                    foreach ($items as $item) {
                        $poItemId = (int)$item['po_item_id'];
                        $receivedQty = (int)$item['received_qty'];
                        $cost = (float)$item['cost'];
                        $sellingPrice = (float)$item['selling_price'];
                        $expiryDate = mysqli_real_escape_string($con, $item['expiry_date']);
                        $itemNotes = mysqli_real_escape_string($con, $item['notes'] ?? '');

                        // Create batch entry
                        $batchQuery = "INSERT INTO product_batch (
                            product_id, batch_number, cost, selling_price,
                            expiry_date, quantity, supplier_id
                        ) SELECT 
                            poi.product_id,
                            CONCAT('$grnNumber-', poi.po_item_id),
                            $cost,
                            $sellingPrice,
                            " . ($expiryDate ? "'$expiryDate'" : "NULL") . ",
                            $receivedQty,
                            po.supplier_id
                        FROM purchase_order_items poi
                        JOIN purchase_orders po ON po.po_id = poi.po_id
                        WHERE poi.po_item_id = $poItemId";

                        if (!insert_query($batchQuery, "Created batch for PO item: $poItemId", "CREATE_BATCH")) {
                            throw new Exception("Failed to create product batch");
                        }

                        $batchId = mysqli_insert_id($con);

                        // Create GRN item
                        $grnItemQuery = "INSERT INTO grn_items (
                            grn_id, po_item_id, batch_id, received_qty,
                            cost, selling_price, expiry_date, notes
                        ) VALUES (
                            $grnId, $poItemId, $batchId, $receivedQty,
                            $cost, $sellingPrice, " . ($expiryDate ? "'$expiryDate'" : "NULL") . ",
                            '$itemNotes'
                        )";

                        if (!insert_query($grnItemQuery, "Added GRN item for PO item: $poItemId", "CREATE_GRN_ITEM")) {
                            throw new Exception("Failed to create GRN item");
                        }

                        // Update PO item received quantity
                        $updatePoItemQuery = "UPDATE purchase_order_items
                            SET received_qty = received_qty + $receivedQty,
                                status = CASE 
                                    WHEN received_qty + $receivedQty >= quantity THEN 'received'
                                    ELSE status
                                END
                            WHERE po_item_id = $poItemId";

                        if (!insert_query($updatePoItemQuery, "Updated received qty for PO item: $poItemId", "UPDATE_PO_ITEM")) {
                            throw new Exception("Failed to update PO item");
                        }
                    }

                    // Check if all items are received and update PO status
                    $checkPoQuery = "UPDATE purchase_orders po
                        SET status = CASE
                            WHEN NOT EXISTS (
                                SELECT 1 FROM purchase_order_items poi
                                WHERE poi.po_id = po.po_id
                                AND poi.status != 'received'
                            ) THEN 'received'
                            ELSE status
                        END
                        WHERE po_id = $poId";

                    if (!insert_query($checkPoQuery, "Updated PO status after GRN", "UPDATE_PO_STATUS")) {
                        throw new Exception("Failed to update PO status");
                    }

                    // Log transaction
                    $poQuery = "SELECT total_amount FROM purchase_orders WHERE po_id = $poId";
                    $result = mysqli_query($con, $poQuery);
                    $poData = mysqli_fetch_assoc($result);

                    transaction_log(
                        'PURCHASE',
                        "GRN created: $grnNumber for PO: $poId",
                        $poData['total_amount']
                    );

                    mysqli_commit($con);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'GRN created successfully',
                        'grn_number' => $grnNumber
                    ]);
                } catch (Exception $e) {
                    mysqli_rollback($con);
                    throw $e;
                }
                break;

            case 'update_grn_status':
                $grnId = (int)$_POST['grn_id'];
                $status = mysqli_real_escape_string($con, $_POST['status']);

                $query = "UPDATE goods_receipt_notes SET status = '$status' WHERE grn_id = $grnId";
                if (!insert_query($query, "Updated GRN status: $status", "UPDATE_GRN_STATUS")) {
                    throw new Exception("Failed to update GRN status");
                }

                echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
                break;
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'get_po_list':
                $query = "SELECT 
                    po.*,
                    s.supplier_name,
                    e.emp_name as created_by_name,
                    (SELECT GROUP_CONCAT(status) FROM purchase_order_items WHERE po_id = po.po_id) as item_statuses
                FROM purchase_orders po
                JOIN suppliers s ON s.supplier_id = po.supplier_id
                JOIN employees e ON e.employ_id = po.created_by
                ORDER BY po.created_at DESC";

                $result = fetch_data($query);
                echo json_encode(['status' => 'success', 'data' => $result]);
                break;

            case 'get_po_details':
                $poId = (int)$_GET['po_id'];

                $poQuery = "SELECT 
                    po.*,
                    s.supplier_name,
                    e.emp_name as created_by_name
                FROM purchase_orders po
                JOIN suppliers s ON s.supplier_id = po.supplier_id
                JOIN employees e ON e.employ_id = po.created_by
                WHERE po.po_id = $poId";

                $itemsQuery = "SELECT 
                    poi.*,
                    p.product_name,
                    p.sku,
                    COALESCE(
                        (SELECT SUM(gi.received_qty)
                        FROM grn_items gi
                        WHERE gi.po_item_id = poi.po_item_id),
                        0
                    ) as total_received_qty
                FROM purchase_order_items poi
                JOIN products p ON p.product_id = poi.product_id
                WHERE poi.po_id = $poId";

                $po = fetch_data($poQuery);
                $items = fetch_data($itemsQuery);

                if (empty($po)) {
                    throw new Exception("Purchase order not found");
                }

                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'po' => $po[0],
                        'items' => $items
                    ]
                ]);
                break;

            case 'get_grn_list':
                $query = "SELECT 
                    grn.*,
                    po.po_number,
                    s.supplier_name,
                    e.emp_name as created_by_name
                FROM goods_receipt_notes grn
                JOIN purchase_orders po ON po.po_id = grn.po_id
                JOIN suppliers s ON s.supplier_id = po.supplier_id
                JOIN employees e ON e.employ_id = grn.created_by
                ORDER BY grn.created_at DESC";

                $result = fetch_data($query);
                echo json_encode(['status' => 'success', 'data' => $result]);
                break;

            case 'get_grn_details':
                $grnId = (int)$_GET['grn_id'];

                $grnQuery = "SELECT 
                    grn.*,
                    po.po_number,
                    s.supplier_name,
                    e.emp_name as created_by_name
                FROM goods_receipt_notes grn
                JOIN purchase_orders po ON po.po_id = grn.po_id
                JOIN suppliers s ON s.supplier_id = po.supplier_id
                JOIN employees e ON e.employ_id = grn.created_by
                WHERE grn.grn_id = $grnId";

                $itemsQuery = "SELECT 
                    gi.*,
                    p.product_name,
                    p.sku,
                    poi.quantity as ordered_qty,
                    poi.unit_cost as ordered_cost,
                    pb.batch_number
                FROM grn_items gi
                JOIN purchase_order_items poi ON poi.po_item_id = gi.po_item_id
                JOIN products p ON p.product_id = poi.product_id
                JOIN product_batch pb ON pb.batch_id = gi.batch_id
                WHERE gi.grn_id = $grnId";

                $grn = fetch_data($grnQuery);
                $items = fetch_data($itemsQuery);

                if (empty($grn)) {
                    throw new Exception("GRN not found");
                }

                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'grn' => $grn[0],
                        'items' => $items
                    ]
                ]);
                break;

            case 'get_products':
                $query = "SELECT 
                    product_id,
                    product_name,
                    sku,
                    cost as last_cost,
                    rate as selling_price
                FROM product_view 
                WHERE has_stock = '1'
                ORDER BY product_name";

                $result = fetch_data($query);
                echo json_encode(['status' => 'success', 'data' => $result]);
                break;

            case 'get_suppliers':
                $query = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name";
                $result = fetch_data($query);
                echo json_encode(['status' => 'success', 'data' => $result]);
                break;
        }
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}