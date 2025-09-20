<?php
/**
 * Purchase Management API
 * Centralized API for all purchase-related operations
 */

require_once('../../inc/config.php');

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the request method and action
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path_parts = explode('/', trim(parse_url($request_uri, PHP_URL_PATH), '/'));
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

/**
 * Handle GET requests
 */
function handleGetRequests($action) {
    global $con;
    
    switch ($action) {
        case 'purchase_orders':
            getPurchaseOrders();
            break;
        case 'purchase_order':
            getPurchaseOrder();
            break;
        case 'purchase_history':
            getPurchaseHistory();
            break;
        case 'outstanding_payments':
            getOutstandingPayments();
            break;
        case 'grn_list':
            getGRNList();
            break;
        case 'grn':
            getGRN();
            break;
        case 'suppliers':
            getSuppliers();
            break;
        case 'supplier':
            getSupplier();
            break;
        case 'dashboard_stats':
            getDashboardStats();
            break;
        case 'purchase_trends':
            getPurchaseTrends();
            break;
        case 'top_suppliers':
            getTopSuppliers();
            break;
        case 'get_pending_orders':
            getPendingOrders();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

/**
 * Handle POST requests
 */
function handlePostRequests($action) {
    switch ($action) {
        case 'create_purchase_order':
            createPurchaseOrder();
            break;
        case 'create_grn':
            createGRN();
            break;
        case 'add_supplier':
            addSupplier();
            break;
        case 'add_payment':
            addSupplierPayment();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

/**
 * Handle PUT requests
 */
function handlePutRequests($action) {
    switch ($action) {
        case 'update_purchase_order':
            updatePurchaseOrder();
            break;
        case 'update_grn':
            updateGRN();
            break;
        case 'update_supplier':
            updateSupplier();
            break;
        case 'approve_purchase_order':
            approvePurchaseOrder();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDeleteRequests($action) {
    switch ($action) {
        case 'delete_purchase_order':
            deletePurchaseOrder();
            break;
        case 'delete_grn':
            deleteGRN();
            break;
        case 'delete_supplier':
            deleteSupplier();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

/**
 * Get all purchase orders with filtering
 */
function getPurchaseOrders() {
    global $con;
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $where_conditions = ['1=1'];
    $params = [];
    $types = '';
    
    // Add filters
    if (!empty($_GET['status'])) {
        $where_conditions[] = 'po.status = ?';
        $params[] = $_GET['status'];
        $types .= 's';
    }
    
    if (!empty($_GET['supplier_id'])) {
        $where_conditions[] = 'po.supplier_id = ?';
        $params[] = $_GET['supplier_id'];
        $types .= 'i';
    }
    
    if (!empty($_GET['date_from'])) {
        $where_conditions[] = 'po.order_date >= ?';
        $params[] = $_GET['date_from'];
        $types .= 's';
    }
    
    if (!empty($_GET['date_to'])) {
        $where_conditions[] = 'po.order_date <= ?';
        $params[] = $_GET['date_to'];
        $types .= 's';
    }
    
    if (!empty($_GET['search'])) {
        $where_conditions[] = '(po.po_number LIKE ? OR s.supplier_name LIKE ? OR po.notes LIKE ?)';
        $search = '%' . $_GET['search'] . '%';
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $types .= 'sss';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total 
                  FROM purchase_orders po 
                  LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id 
                  WHERE $where_clause";
                  
    $count_stmt = mysqli_prepare($con, $count_sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    }
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    // Get purchase orders
    $sql = "SELECT po.*, s.supplier_name, s.supplier_tel, s.supplier_address,
                   e.emp_name as created_by_name,
                   COUNT(poi.po_item_id) as item_count,
                   0 as received_items
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
            LEFT JOIN employees e ON po.created_by = e.employ_id
            LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
            WHERE $where_clause
            GROUP BY po.po_id
            ORDER BY po.created_at DESC
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
    
    $purchase_orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['can_create_grn'] = ($row['status'] === 'approved' || $row['status'] === 'ordered') && 
                                 $row['received_items'] < $row['item_count'];
        $purchase_orders[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $purchase_orders,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total_records,
            'total_pages' => ceil($total_records / $limit)
        ]
    ]);
}

/**
 * Get single purchase order with items
 */
function getPurchaseOrder() {
    global $con;
    
    $po_id = isset($_GET['po_id']) ? intval($_GET['po_id']) : 0;
    if (!$po_id) {
        throw new Exception('Purchase Order ID is required', 400);
    }
    
    // Get purchase order details
    $sql = "SELECT po.*, s.supplier_name, s.supplier_tel, s.supplier_address, s.credit_balance,
                   e.emp_name as created_by_name
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
            LEFT JOIN employees e ON po.created_by = e.employ_id
            WHERE po.po_id = ?";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $po_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$purchase_order = mysqli_fetch_assoc($result)) {
        throw new Exception('Purchase Order not found', 404);
    }
    
    // Get purchase order items
    $items_sql = "SELECT poi.*, p.product_name, p.unit, p.category,
                         b.batch_name, b.quantity as stock_quantity
                  FROM purchase_order_items poi
                  LEFT JOIN products p ON poi.product_id = p.product_id
                  LEFT JOIN batches b ON p.product_id = b.product_id
                  WHERE poi.po_id = ?
                  ORDER BY poi.po_item_id";
    
    $items_stmt = mysqli_prepare($con, $items_sql);
    mysqli_stmt_bind_param($items_stmt, 'i', $po_id);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    
    $items = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $item['remaining_qty'] = $item['quantity'] - $item['received_qty'];
        $items[] = $item;
    }
    
    // Get GRNs for this PO
    $grn_sql = "SELECT grn.*, COUNT(gi.grn_item_id) as item_count
                FROM goods_receipt_notes grn
                LEFT JOIN grn_items gi ON grn.grn_id = gi.grn_id
                WHERE grn.po_id = ?
                GROUP BY grn.grn_id
                ORDER BY grn.created_at DESC";
    
    $grn_stmt = mysqli_prepare($con, $grn_sql);
    mysqli_stmt_bind_param($grn_stmt, 'i', $po_id);
    mysqli_stmt_execute($grn_stmt);
    $grn_result = mysqli_stmt_get_result($grn_stmt);
    
    $grns = [];
    while ($grn = mysqli_fetch_assoc($grn_result)) {
        $grns[] = $grn;
    }
    
    $purchase_order['items'] = $items;
    $purchase_order['grns'] = $grns;
    
    echo json_encode([
        'success' => true,
        'data' => $purchase_order
    ]);
}

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    global $con;
    
    $stats = [];
    
    // Total Purchase Orders
    $sql = "SELECT COUNT(*) as total FROM purchase_orders";
    $result = mysqli_query($con, $sql);
    $stats['total_pos'] = mysqli_fetch_assoc($result)['total'];
    
    // Pending GRNs (POs that are approved but don't have completed GRNs)
    $sql = "SELECT COUNT(DISTINCT po.po_id) as pending_grns
            FROM purchase_orders po
            LEFT JOIN goods_receipt_notes grn ON po.po_id = grn.po_id AND grn.status = 'completed'
            WHERE po.status IN ('approved', 'ordered') AND grn.grn_id IS NULL";
    $result = mysqli_query($con, $sql);
    $stats['pending_grns'] = mysqli_fetch_assoc($result)['pending_grns'];
    
    // Total Amount This Month
    $sql = "SELECT COALESCE(SUM(total_amount), 0) as total_amount
            FROM purchase_orders
            WHERE order_date >= DATE_FORMAT(NOW(), '%Y-%m-01')
            AND status != 'cancelled'";
    $result = mysqli_query($con, $sql);
    $stats['monthly_amount'] = mysqli_fetch_assoc($result)['total_amount'];
    
    // Outstanding Payments
    $sql = "SELECT COALESCE(SUM(s.credit_balance), 0) as outstanding
            FROM suppliers s
            WHERE s.credit_balance > 0";
    $result = mysqli_query($con, $sql);
    $stats['outstanding_payments'] = mysqli_fetch_assoc($result)['outstanding'];
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}

/**
 * Create new purchase order
 */
function createPurchaseOrder() {
    global $con;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Invalid JSON data', 400);
    }
    
    // Validate required fields
    $required = ['supplier_id', 'order_date', 'items'];
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
        // Generate PO number
        $po_number = generatePONumber($con);
        
        // Calculate totals
        $subtotal = 0;
        foreach ($input['items'] as $item) {
            if (!isset($item['product_id'], $item['quantity'], $item['unit_cost'])) {
                throw new Exception('Invalid item data', 400);
            }
            $subtotal += $item['quantity'] * $item['unit_cost'];
        }
        
        $discount_amount = 0;
        if (!empty($input['discount_value'])) {
            if ($input['discount_type'] === 'percentage') {
                $discount_amount = ($subtotal * $input['discount_value']) / 100;
            } else {
                $discount_amount = $input['discount_value'];
            }
        }
        
        $tax_amount = 0;
        if (!empty($input['tax_value'])) {
            $taxable_amount = $subtotal - $discount_amount;
            if ($input['tax_type'] === 'percentage') {
                $tax_amount = ($taxable_amount * $input['tax_value']) / 100;
            } else {
                $tax_amount = $input['tax_value'];
            }
        }
        
        $shipping_fee = isset($input['shipping_fee']) ? $input['shipping_fee'] : 0;
        $total_amount = $subtotal - $discount_amount + $tax_amount + $shipping_fee;
        
        // Insert purchase order
        $po_sql = "INSERT INTO purchase_orders (
                    po_number, supplier_id, order_date, delivery_date, shipping_fee,
                    discount_type, discount_value, tax_type, tax_value, status,
                    subtotal, total_discount, total_tax, total_amount, notes, created_by
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $po_stmt = mysqli_prepare($con, $po_sql);
        mysqli_stmt_bind_param($po_stmt, 'sissssssssddddsi',
            $po_number,
            $input['supplier_id'],
            $input['order_date'],
            $input['delivery_date'] ?? null,
            $shipping_fee,
            $input['discount_type'] ?? 'fixed',
            $input['discount_value'] ?? 0,
            $input['tax_type'] ?? 'percentage',
            $input['tax_value'] ?? 0,
            $input['status'] ?? 'draft',
            $subtotal,
            $discount_amount,
            $tax_amount,
            $total_amount,
            $input['notes'] ?? '',
            $_SESSION['employee_id'] ?? 1
        );
        
        if (!mysqli_stmt_execute($po_stmt)) {
            throw new Exception('Failed to create purchase order');
        }
        
        $po_id = mysqli_insert_id($con);
        
        // Insert purchase order items
        $item_sql = "INSERT INTO purchase_order_items (po_id, product_id, quantity, unit_cost, total_cost, status) VALUES (?, ?, ?, ?, ?, ?)";
        $item_stmt = mysqli_prepare($con, $item_sql);
        
        foreach ($input['items'] as $item) {
            $total_cost = $item['quantity'] * $item['unit_cost'];
            mysqli_stmt_bind_param($item_stmt, 'iiddds',
                $po_id,
                $item['product_id'],
                $item['quantity'],
                $item['unit_cost'],
                $total_cost,
                'pending'
            );
            
            if (!mysqli_stmt_execute($item_stmt)) {
                throw new Exception('Failed to add purchase order item');
            }
        }
        
        mysqli_commit($con);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'po_id' => $po_id,
                'po_number' => $po_number,
                'total_amount' => $total_amount
            ],
            'message' => 'Purchase order created successfully'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

/**
 * Generate unique PO number
 */
function generatePONumber($con) {
    $prefix = 'PO' . date('Ym');
    
    $sql = "SELECT po_number FROM purchase_orders WHERE po_number LIKE ? ORDER BY po_number DESC LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    $search = $prefix . '%';
    mysqli_stmt_bind_param($stmt, 's', $search);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $last_number = intval(substr($row['po_number'], -4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}

/**
 * Get purchase history with advanced filtering
 */
function getPurchaseHistory() {
    global $con;
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $where_conditions = ['1=1'];
    $params = [];
    $types = '';
    
    // Date range filter
    if (!empty($_GET['date_from'])) {
        $where_conditions[] = 'po.order_date >= ?';
        $params[] = $_GET['date_from'];
        $types .= 's';
    }
    
    if (!empty($_GET['date_to'])) {
        $where_conditions[] = 'po.order_date <= ?';
        $params[] = $_GET['date_to'];
        $types .= 's';
    }
    
    // Status filter
    if (!empty($_GET['status'])) {
        $where_conditions[] = 'po.status = ?';
        $params[] = $_GET['status'];
        $types .= 's';
    }
    
    // Supplier filter
    if (!empty($_GET['supplier_id'])) {
        $where_conditions[] = 'po.supplier_id = ?';
        $params[] = $_GET['supplier_id'];
        $types .= 'i';
    }
    
    // Amount range filter
    if (!empty($_GET['amount_from'])) {
        $where_conditions[] = 'po.total_amount >= ?';
        $params[] = $_GET['amount_from'];
        $types .= 'd';
    }
    
    if (!empty($_GET['amount_to'])) {
        $where_conditions[] = 'po.total_amount <= ?';
        $params[] = $_GET['amount_to'];
        $types .= 'd';
    }
    
    // Search filter
    if (!empty($_GET['search'])) {
        $where_conditions[] = '(po.po_number LIKE ? OR s.supplier_name LIKE ? OR po.notes LIKE ?)';
        $search = '%' . $_GET['search'] . '%';
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $types .= 'sss';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total 
                  FROM purchase_orders po 
                  LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id 
                  WHERE $where_clause";
    
    $count_stmt = mysqli_prepare($con, $count_sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    }
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    // Get purchase history
    $sql = "SELECT po.*, s.supplier_name, s.supplier_tel,
                   e.emp_name as created_by_name,
                   COUNT(DISTINCT grn.grn_id) as grn_count,
                   SUM(sp.amount) as paid_amount
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
            LEFT JOIN employees e ON po.created_by = e.employ_id
            LEFT JOIN goods_receipt_notes grn ON po.po_id = grn.po_id
            LEFT JOIN supplier_payments sp ON po.po_id = sp.po_id
            WHERE $where_clause
            GROUP BY po.po_id
            ORDER BY po.order_date DESC
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
    
    $history = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['paid_amount'] = $row['paid_amount'] ?? 0;
        $row['outstanding_amount'] = $row['total_amount'] - $row['paid_amount'];
        $history[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $history,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total_records,
            'total_pages' => ceil($total_records / $limit)
        ]
    ]);
}

/**
 * Get outstanding payments to suppliers
 */
function getOutstandingPayments() {
    global $con;
    
    $sql = "SELECT s.supplier_id, s.supplier_name, s.supplier_tel, s.credit_balance,
                   COUNT(DISTINCT po.po_id) as pending_orders,
                   SUM(CASE WHEN po.status IN ('approved', 'ordered', 'received') 
                       THEN po.total_amount ELSE 0 END) as total_pending_amount,
                   MAX(po.order_date) as last_order_date,
                   COALESCE(SUM(sp.amount), 0) as total_paid
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            LEFT JOIN supplier_payments sp ON s.supplier_id = sp.supplier_id
            WHERE s.credit_balance > 0 OR po.po_id IS NOT NULL
            GROUP BY s.supplier_id
            HAVING s.credit_balance > 0 OR (total_pending_amount - total_paid) > 0
            ORDER BY s.credit_balance DESC";
    
    $result = mysqli_query($con, $sql);
    
    $outstanding = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['actual_outstanding'] = max($row['credit_balance'], 
                                        $row['total_pending_amount'] - $row['total_paid']);
        $outstanding[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $outstanding
    ]);
}

/**
 * Add supplier payment
 */
function addSupplierPayment() {
    global $con;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Invalid JSON data', 400);
    }
    
    $required = ['supplier_id', 'amount', 'method'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("$field is required", 400);
        }
    }
    
    if ($input['amount'] <= 0) {
        throw new Exception('Amount must be greater than 0', 400);
    }
    
    mysqli_begin_transaction($con);
    
    try {
        // Insert payment record
        $payment_sql = "INSERT INTO supplier_payments (
                        supplier_id, date, amount, method, reference_no, note, created_by, po_id
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $payment_stmt = mysqli_prepare($con, $payment_sql);
        mysqli_stmt_bind_param($payment_stmt, 'isdsssii',
            $input['supplier_id'],
            $input['date'] ?? date('Y-m-d'),
            $input['amount'],
            $input['method'],
            $input['reference_no'] ?? null,
            $input['note'] ?? '',
            $_SESSION['employee_id'] ?? 1,
            $input['po_id'] ?? null
        );
        
        if (!mysqli_stmt_execute($payment_stmt)) {
            throw new Exception('Failed to record payment');
        }
        
        // Update supplier credit balance
        $update_sql = "UPDATE suppliers SET credit_balance = credit_balance - ? WHERE supplier_id = ?";
        $update_stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($update_stmt, 'di', $input['amount'], $input['supplier_id']);
        
        if (!mysqli_stmt_execute($update_stmt)) {
            throw new Exception('Failed to update supplier balance');
        }
        
        mysqli_commit($con);
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment recorded successfully'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

/**
 * Get all suppliers
 */
function getSuppliers() {
    global $con;
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $active_only = isset($_GET['active_only']) ? filter_var($_GET['active_only'], FILTER_VALIDATE_BOOLEAN) : false;
    
    $where_conditions = ['1=1'];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $where_conditions[] = '(supplier_name LIKE ? OR supplier_tel LIKE ? OR supplier_address LIKE ?)';
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'sss';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $sql = "SELECT s.*, COUNT(DISTINCT po.po_id) as total_orders,
                   SUM(CASE WHEN po.status != 'cancelled' THEN po.total_amount ELSE 0 END) as total_amount,
                   MAX(po.order_date) as last_order_date
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            WHERE $where_clause
            GROUP BY s.supplier_id
            ORDER BY s.supplier_name";
    
    $stmt = mysqli_prepare($con, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $suppliers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $suppliers
    ]);
}

/**
 * Get pending purchase orders for a specific supplier
 */
function getPendingOrders() {
    global $con;
    
    $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
    
    if (!$supplier_id) {
        throw new Exception('Supplier ID is required', 400);
    }
    
    $sql = "SELECT 
                po.po_id,
                po.po_number,
                po.order_date,
                po.delivery_date,
                po.status,
                po.total_amount,
                po.notes
            FROM purchase_orders po
            WHERE po.supplier_id = ? 
            AND po.status IN ('pending', 'ordered')
            ORDER BY po.order_date DESC";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $supplier_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $orders
    ]);
}

/**
 * Get purchase trends for chart
 */
function getPurchaseTrends() {
    global $con;
    
    $sql = "SELECT 
                DATE_FORMAT(order_date, '%Y-%m') as month,
                SUM(total_amount) as total_amount,
                COUNT(*) as order_count
            FROM purchase_orders 
            WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(order_date, '%Y-%m')
            ORDER BY month ASC";
    
    $result = mysqli_query($con, $sql);
    
    if (!$result) {
        throw new Exception('Failed to get purchase trends: ' . mysqli_error($con));
    }
    
    $trends = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $trends[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $trends
    ]);
}

/**
 * Get top suppliers for chart
 */
function getTopSuppliers() {
    global $con;
    
    $sql = "SELECT s.supplier_name, 
                   SUM(po.total_amount) as total_amount,
                   COUNT(po.po_id) as order_count
            FROM suppliers s
            INNER JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            WHERE po.order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY s.supplier_id
            ORDER BY total_amount DESC
            LIMIT 5";
    
    $result = mysqli_query($con, $sql);
    
    if (!$result) {
        throw new Exception('Failed to get top suppliers: ' . mysqli_error($con));
    }
    
    $suppliers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $suppliers
    ]);
}

?>