<?php
/**
 * Goods Receipt Note (GRN) API
 * Handles all GRN-related operations
 */

require_once('../../inc/config.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($method) {
        case 'GET':
            handleGetRequests($action);
            break;
        case 'POST':
            handlePostRequests($action);
            break;
        case 'PUT':
            handlePutRequests($action);
            break;
        case 'DELETE':
            handleDeleteRequests($action);
            break;
        default:
            throw new Exception('Method not allowed', 405);
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function handleGetRequests($action) {
    switch ($action) {
        case 'list':
            getGRNList();
            break;
        case 'grn':
            getGRN();
            break;
        case 'pending_pos':
            getPendingPurchaseOrders();
            break;
        case 'po_items':
            getPOItems();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function handlePostRequests($action) {
    switch ($action) {
        case 'create':
            createGRN();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function handlePutRequests($action) {
    switch ($action) {
        case 'update':
            updateGRN();
            break;
        case 'complete':
            completeGRN();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function handleDeleteRequests($action) {
    switch ($action) {
        case 'delete':
            deleteGRN();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

/**
 * Get list of GRNs with filtering
 */
function getGRNList() {
    global $con;
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $where_conditions = ['1=1'];
    $params = [];
    $types = '';
    
    // Status filter
    if (!empty($_GET['status'])) {
        $where_conditions[] = 'grn.status = ?';
        $params[] = $_GET['status'];
        $types .= 's';
    }
    
    // Date range filter
    if (!empty($_GET['date_from'])) {
        $where_conditions[] = 'grn.receipt_date >= ?';
        $params[] = $_GET['date_from'];
        $types .= 's';
    }
    
    if (!empty($_GET['date_to'])) {
        $where_conditions[] = 'grn.receipt_date <= ?';
        $params[] = $_GET['date_to'];
        $types .= 's';
    }
    
    // Supplier filter
    if (!empty($_GET['supplier_id'])) {
        $where_conditions[] = 'po.supplier_id = ?';
        $params[] = $_GET['supplier_id'];
        $types .= 'i';
    }
    
    // Search filter
    if (!empty($_GET['search'])) {
        $where_conditions[] = '(grn.grn_number LIKE ? OR grn.invoice_number LIKE ? OR po.po_number LIKE ? OR s.supplier_name LIKE ?)';
        $search = '%' . $_GET['search'] . '%';
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $types .= 'ssss';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total 
                  FROM goods_receipt_notes grn 
                  LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
                  LEFT JOIN suppliers s ON COALESCE(grn.supplier_id, po.supplier_id) = s.supplier_id
                  WHERE $where_clause";
    
    $count_stmt = mysqli_prepare($con, $count_sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    }
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    // Get GRNs
    $sql = "SELECT grn.*, po.po_number, po.order_date, po.total_amount as po_amount,
                   s.supplier_name, s.supplier_tel,
                   e.emp_name as created_by_name,
                   COUNT(gi.grn_item_id) as item_count,
                   SUM(gi.received_qty * gi.cost) as grn_total
            FROM goods_receipt_notes grn
            LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
            LEFT JOIN suppliers s ON COALESCE(grn.supplier_id, po.supplier_id) = s.supplier_id
            LEFT JOIN employees e ON grn.created_by = e.employ_id
            LEFT JOIN grn_items gi ON grn.grn_id = gi.grn_id
            WHERE $where_clause
            GROUP BY grn.grn_id
            ORDER BY grn.created_at DESC
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = mysqli_prepare($con, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $grns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $grns[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $grns,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total_records,
            'total_pages' => ceil($total_records / $limit)
        ]
    ]);
}

/**
 * Get single GRN with items
 */
function getGRN() {
    global $con;
    
    $grn_id = isset($_GET['grn_id']) ? intval($_GET['grn_id']) : 0;
    if (!$grn_id) {
        throw new Exception('GRN ID is required', 400);
    }
    
    // Get GRN details
    $sql = "SELECT grn.*, po.po_number, po.order_date, po.total_amount as po_amount,
                   s.supplier_name, s.supplier_tel, s.supplier_address,
                   e.emp_name as created_by_name
            FROM goods_receipt_notes grn
            LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
            LEFT JOIN suppliers s ON COALESCE(grn.supplier_id, po.supplier_id) = s.supplier_id
            LEFT JOIN employees e ON grn.created_by = e.employ_id
            WHERE grn.grn_id = ?";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $grn_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$grn = mysqli_fetch_assoc($result)) {
        throw new Exception('GRN not found', 404);
    }
    
    // Get GRN items
    $items_sql = "SELECT gi.*, poi.quantity as ordered_qty, poi.unit_cost as ordered_cost,
                         p.product_name, p.unit, p.category,
                         b.batch_name, b.quantity as current_stock
                  FROM grn_items gi
                  LEFT JOIN purchase_order_items poi ON gi.po_item_id = poi.po_item_id
                  LEFT JOIN products p ON poi.product_id = p.product_id
                  LEFT JOIN batches b ON gi.batch_id = b.batch_id
                  WHERE gi.grn_id = ?
                  ORDER BY gi.grn_item_id";
    
    $items_stmt = mysqli_prepare($con, $items_sql);
    mysqli_stmt_bind_param($items_stmt, 'i', $grn_id);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    
    $items = [];
    $total_amount = 0;
    while ($item = mysqli_fetch_assoc($items_result)) {
        $item['total_cost'] = $item['received_qty'] * $item['cost'];
        $total_amount += $item['total_cost'];
        $items[] = $item;
    }
    
    $grn['items'] = $items;
    $grn['total_amount'] = $total_amount;
    
    echo json_encode([
        'success' => true,
        'data' => $grn
    ]);
}

/**
 * Get purchase orders that can have GRNs created
 */
function getPendingPurchaseOrders() {
    global $con;
    
    $sql = "SELECT po.po_id, po.po_number, po.order_date, po.total_amount,
                   s.supplier_name,
                   COUNT(poi.po_item_id) as total_items,
                   SUM(CASE WHEN poi.received_qty >= poi.quantity THEN 1 ELSE 0 END) as completed_items
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
            LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
            WHERE po.status IN ('approved', 'ordered')
            GROUP BY po.po_id
            HAVING completed_items < total_items
            ORDER BY po.order_date ASC";
    
    $result = mysqli_query($con, $sql);
    
    $purchase_orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['pending_items'] = $row['total_items'] - $row['completed_items'];
        $purchase_orders[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $purchase_orders
    ]);
}

/**
 * Get items for a specific PO that can be included in GRN
 */
function getPOItems() {
    global $con;
    
    $po_id = isset($_GET['po_id']) ? intval($_GET['po_id']) : 0;
    if (!$po_id) {
        throw new Exception('Purchase Order ID is required', 400);
    }
    
    $sql = "SELECT poi.*, p.product_name, p.unit, p.category,
                   (poi.quantity - poi.received_qty) as remaining_qty
            FROM purchase_order_items poi
            LEFT JOIN products p ON poi.product_id = p.product_id
            WHERE poi.po_id = ? AND (poi.quantity - poi.received_qty) > 0
            ORDER BY p.product_name";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $po_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $items
    ]);
}

/**
 * Create new GRN
 */
function createGRN() {
    global $con;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Invalid JSON data', 400);
    }
    
    // Validate required fields
    $required = ['po_id', 'receipt_date', 'items'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("$field is required", 400);
        }
    }
    
    if (!is_array($input['items']) || empty($input['items'])) {
        throw new Exception('At least one item is required', 400);
    }
    
    mysqli_begin_transaction($con);
    
    try {
        // Generate GRN number
        $grn_number = generateGRNNumber($con);
        
        // Insert GRN
        $grn_sql = "INSERT INTO goods_receipt_notes (
                    grn_number, po_id, receipt_date, invoice_number, invoice_date, notes, status, created_by
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $grn_stmt = mysqli_prepare($con, $grn_sql);
        mysqli_stmt_bind_param($grn_stmt, 'sisssssi',
            $grn_number,
            $input['po_id'],
            $input['receipt_date'],
            $input['invoice_number'] ?? null,
            $input['invoice_date'] ?? null,
            $input['notes'] ?? '',
            $input['status'] ?? 'draft',
            $_SESSION['employee_id'] ?? 1
        );
        
        if (!mysqli_stmt_execute($grn_stmt)) {
            throw new Exception('Failed to create GRN');
        }
        
        $grn_id = mysqli_insert_id($con);
        
        // Process GRN items
        foreach ($input['items'] as $item) {
            if (!isset($item['po_item_id'], $item['received_qty'], $item['cost'])) {
                throw new Exception('Invalid item data', 400);
            }
            
            if ($item['received_qty'] <= 0) {
                continue; // Skip items with zero or negative quantity
            }
            
            // Get or create batch for the product
            $batch_id = getOrCreateBatch($con, $item, $grn_id);
            
            // Insert GRN item
            $grn_item_sql = "INSERT INTO grn_items (
                            grn_id, po_item_id, batch_id, received_qty, cost, selling_price, expiry_date, notes
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $grn_item_stmt = mysqli_prepare($con, $grn_item_sql);
            mysqli_stmt_bind_param($grn_item_stmt, 'iiidddss',
                $grn_id,
                $item['po_item_id'],
                $batch_id,
                $item['received_qty'],
                $item['cost'],
                $item['selling_price'] ?? 0,
                $item['expiry_date'] ?? null,
                $item['notes'] ?? ''
            );
            
            if (!mysqli_stmt_execute($grn_item_stmt)) {
                throw new Exception('Failed to add GRN item');
            }
            
            // Update PO item received quantity
            $update_po_item_sql = "UPDATE purchase_order_items 
                                  SET received_qty = received_qty + ?,
                                      status = CASE 
                                        WHEN (received_qty + ?) >= quantity THEN 'received'
                                        ELSE 'approved'
                                      END
                                  WHERE po_item_id = ?";
            
            $update_po_item_stmt = mysqli_prepare($con, $update_po_item_sql);
            mysqli_stmt_bind_param($update_po_item_stmt, 'ddi',
                $item['received_qty'],
                $item['received_qty'],
                $item['po_item_id']
            );
            
            if (!mysqli_stmt_execute($update_po_item_stmt)) {
                throw new Exception('Failed to update PO item');
            }
            
            // Update batch stock if GRN is completed
            if (($input['status'] ?? 'draft') === 'completed') {
                $update_batch_sql = "UPDATE batches SET quantity = quantity + ? WHERE batch_id = ?";
                $update_batch_stmt = mysqli_prepare($con, $update_batch_sql);
                mysqli_stmt_bind_param($update_batch_stmt, 'di', $item['received_qty'], $batch_id);
                mysqli_stmt_execute($update_batch_stmt);
            }
        }
        
        // Update PO status if all items are received
        $check_po_sql = "SELECT COUNT(*) as total_items,
                               SUM(CASE WHEN received_qty >= quantity THEN 1 ELSE 0 END) as completed_items
                        FROM purchase_order_items
                        WHERE po_id = ?";
        
        $check_po_stmt = mysqli_prepare($con, $check_po_sql);
        mysqli_stmt_bind_param($check_po_stmt, 'i', $input['po_id']);
        mysqli_stmt_execute($check_po_stmt);
        $po_result = mysqli_stmt_get_result($check_po_stmt);
        $po_data = mysqli_fetch_assoc($po_result);
        
        if ($po_data['total_items'] == $po_data['completed_items']) {
            $update_po_sql = "UPDATE purchase_orders SET status = 'received' WHERE po_id = ?";
            $update_po_stmt = mysqli_prepare($con, $update_po_sql);
            mysqli_stmt_bind_param($update_po_stmt, 'i', $input['po_id']);
            mysqli_stmt_execute($update_po_stmt);
        }
        
        mysqli_commit($con);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'grn_id' => $grn_id,
                'grn_number' => $grn_number
            ],
            'message' => 'GRN created successfully'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

/**
 * Generate unique GRN number
 */
function generateGRNNumber($con) {
    $prefix = 'GRN' . date('Ym');
    
    $sql = "SELECT grn_number FROM goods_receipt_notes WHERE grn_number LIKE ? ORDER BY grn_number DESC LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    $search = $prefix . '%';
    mysqli_stmt_bind_param($stmt, 's', $search);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $last_number = intval(substr($row['grn_number'], -4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}

/**
 * Get or create batch for product
 */
function getOrCreateBatch($con, $item, $grn_id) {
    // Get product ID from PO item
    $product_sql = "SELECT product_id FROM purchase_order_items WHERE po_item_id = ?";
    $product_stmt = mysqli_prepare($con, $product_sql);
    mysqli_stmt_bind_param($product_stmt, 'i', $item['po_item_id']);
    mysqli_stmt_execute($product_stmt);
    $product_result = mysqli_stmt_get_result($product_stmt);
    $product_data = mysqli_fetch_assoc($product_result);
    
    if (!$product_data) {
        throw new Exception('Product not found for PO item');
    }
    
    $product_id = $product_data['product_id'];
    
    // Check if batch already exists for this product
    if (!empty($item['batch_id'])) {
        return $item['batch_id'];
    }
    
    // Create new batch
    $batch_name = 'BATCH-' . $grn_id . '-' . $product_id . '-' . date('Ymd');
    
    $batch_sql = "INSERT INTO batches (product_id, batch_name, quantity, cost_price, selling_price, expiry_date) 
                  VALUES (?, ?, 0, ?, ?, ?)";
    
    $batch_stmt = mysqli_prepare($con, $batch_sql);
    mysqli_stmt_bind_param($batch_stmt, 'isdds',
        $product_id,
        $batch_name,
        $item['cost'],
        $item['selling_price'] ?? 0,
        $item['expiry_date'] ?? null
    );
    
    if (!mysqli_stmt_execute($batch_stmt)) {
        throw new Exception('Failed to create batch');
    }
    
    return mysqli_insert_id($con);
}

/**
 * Complete GRN (update stock levels)
 */
function completeGRN() {
    global $con;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['grn_id'])) {
        throw new Exception('GRN ID is required', 400);
    }
    
    $grn_id = intval($input['grn_id']);
    
    mysqli_begin_transaction($con);
    
    try {
        // Check if GRN exists and is not already completed
        $check_sql = "SELECT status FROM goods_receipt_notes WHERE grn_id = ?";
        $check_stmt = mysqli_prepare($con, $check_sql);
        mysqli_stmt_bind_param($check_stmt, 'i', $grn_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $grn_data = mysqli_fetch_assoc($check_result);
        
        if (!$grn_data) {
            throw new Exception('GRN not found', 404);
        }
        
        if ($grn_data['status'] === 'completed') {
            throw new Exception('GRN is already completed', 400);
        }
        
        // Update stock levels for all GRN items
        $items_sql = "SELECT gi.batch_id, gi.received_qty 
                     FROM grn_items gi 
                     WHERE gi.grn_id = ?";
        
        $items_stmt = mysqli_prepare($con, $items_sql);
        mysqli_stmt_bind_param($items_stmt, 'i', $grn_id);
        mysqli_stmt_execute($items_stmt);
        $items_result = mysqli_stmt_get_result($items_stmt);
        
        while ($item = mysqli_fetch_assoc($items_result)) {
            $update_stock_sql = "UPDATE batches SET quantity = quantity + ? WHERE batch_id = ?";
            $update_stock_stmt = mysqli_prepare($con, $update_stock_sql);
            mysqli_stmt_bind_param($update_stock_stmt, 'di', $item['received_qty'], $item['batch_id']);
            
            if (!mysqli_stmt_execute($update_stock_stmt)) {
                throw new Exception('Failed to update stock levels');
            }
        }
        
        // Update GRN status
        $update_grn_sql = "UPDATE goods_receipt_notes SET status = 'completed' WHERE grn_id = ?";
        $update_grn_stmt = mysqli_prepare($con, $update_grn_sql);
        mysqli_stmt_bind_param($update_grn_stmt, 'i', $grn_id);
        
        if (!mysqli_stmt_execute($update_grn_stmt)) {
            throw new Exception('Failed to update GRN status');
        }
        
        mysqli_commit($con);
        
        echo json_encode([
            'success' => true,
            'message' => 'GRN completed and stock levels updated'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

/**
 * Delete GRN
 */
function deleteGRN() {
    global $con;
    
    $grn_id = isset($_GET['grn_id']) ? intval($_GET['grn_id']) : 0;
    if (!$grn_id) {
        throw new Exception('GRN ID is required', 400);
    }
    
    mysqli_begin_transaction($con);
    
    try {
        // Check if GRN is completed
        $check_sql = "SELECT status, po_id FROM goods_receipt_notes WHERE grn_id = ?";
        $check_stmt = mysqli_prepare($con, $check_sql);
        mysqli_stmt_bind_param($check_stmt, 'i', $grn_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $grn_data = mysqli_fetch_assoc($check_result);
        
        if (!$grn_data) {
            throw new Exception('GRN not found', 404);
        }
        
        if ($grn_data['status'] === 'completed') {
            // Reverse stock updates
            $items_sql = "SELECT gi.batch_id, gi.received_qty 
                         FROM grn_items gi 
                         WHERE gi.grn_id = ?";
            
            $items_stmt = mysqli_prepare($con, $items_sql);
            mysqli_stmt_bind_param($items_stmt, 'i', $grn_id);
            mysqli_stmt_execute($items_stmt);
            $items_result = mysqli_stmt_get_result($items_stmt);
            
            while ($item = mysqli_fetch_assoc($items_result)) {
                $update_stock_sql = "UPDATE batches SET quantity = quantity - ? WHERE batch_id = ?";
                $update_stock_stmt = mysqli_prepare($con, $update_stock_sql);
                mysqli_stmt_bind_param($update_stock_stmt, 'di', $item['received_qty'], $item['batch_id']);
                mysqli_stmt_execute($update_stock_stmt);
            }
        }
        
        // Reverse PO item updates
        $reverse_po_sql = "UPDATE purchase_order_items poi
                          INNER JOIN grn_items gi ON poi.po_item_id = gi.po_item_id
                          SET poi.received_qty = poi.received_qty - gi.received_qty,
                              poi.status = CASE 
                                WHEN (poi.received_qty - gi.received_qty) <= 0 THEN 'approved'
                                ELSE poi.status
                              END
                          WHERE gi.grn_id = ?";
        
        $reverse_po_stmt = mysqli_prepare($con, $reverse_po_sql);
        mysqli_stmt_bind_param($reverse_po_stmt, 'i', $grn_id);
        mysqli_stmt_execute($reverse_po_stmt);
        
        // Delete GRN items
        $delete_items_sql = "DELETE FROM grn_items WHERE grn_id = ?";
        $delete_items_stmt = mysqli_prepare($con, $delete_items_sql);
        mysqli_stmt_bind_param($delete_items_stmt, 'i', $grn_id);
        mysqli_stmt_execute($delete_items_stmt);
        
        // Delete GRN
        $delete_grn_sql = "DELETE FROM goods_receipt_notes WHERE grn_id = ?";
        $delete_grn_stmt = mysqli_prepare($con, $delete_grn_sql);
        mysqli_stmt_bind_param($delete_grn_stmt, 'i', $grn_id);
        mysqli_stmt_execute($delete_grn_stmt);
        
        // Update PO status if needed
        $check_po_sql = "SELECT COUNT(*) as total_items,
                               SUM(CASE WHEN received_qty >= quantity THEN 1 ELSE 0 END) as completed_items
                        FROM purchase_order_items
                        WHERE po_id = ?";
        
        $check_po_stmt = mysqli_prepare($con, $check_po_sql);
        mysqli_stmt_bind_param($check_po_stmt, 'i', $grn_data['po_id']);
        mysqli_stmt_execute($check_po_stmt);
        $po_result = mysqli_stmt_get_result($check_po_stmt);
        $po_data = mysqli_fetch_assoc($po_result);
        
        $new_status = ($po_data['completed_items'] > 0) ? 'ordered' : 'approved';
        
        $update_po_sql = "UPDATE purchase_orders SET status = ? WHERE po_id = ?";
        $update_po_stmt = mysqli_prepare($con, $update_po_sql);
        mysqli_stmt_bind_param($update_po_stmt, 'si', $new_status, $grn_data['po_id']);
        mysqli_stmt_execute($update_po_stmt);
        
        mysqli_commit($con);
        
        echo json_encode([
            'success' => true,
            'message' => 'GRN deleted successfully'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

?>