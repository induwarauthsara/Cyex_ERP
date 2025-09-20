<?php
/**
 * Supplier Management API
 * Handles all supplier-related operations
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
            getSuppliers();
            break;
        case 'supplier':
            getSupplier();
            break;
        case 'search':
            searchSuppliers();
            break;
        case 'payments':
            getSupplierPayments();
            break;
        case 'outstanding':
            getOutstandingPayments();
            break;
        case 'statistics':
            getSupplierStatistics();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function handlePostRequests($action) {
    switch ($action) {
        case 'create':
            createSupplier();
            break;
        case 'payment':
            addPayment();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function handlePutRequests($action) {
    switch ($action) {
        case 'update':
            updateSupplier();
            break;
        case 'update_balance':
            updateCreditBalance();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function handleDeleteRequests($action) {
    switch ($action) {
        case 'delete':
            deleteSupplier();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

/**
 * Get all suppliers with filtering and pagination
 */
function getSuppliers() {
    global $con;
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $where_conditions = ['1=1'];
    $params = [];
    $types = '';
    
    // Search filter
    if (!empty($_GET['search'])) {
        $where_conditions[] = '(s.supplier_name LIKE ? OR s.supplier_tel LIKE ? OR s.supplier_address LIKE ?)';
        $search = '%' . $_GET['search'] . '%';
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $types .= 'sss';
    }
    
    // Outstanding balance filter
    if (isset($_GET['has_outstanding']) && $_GET['has_outstanding'] == '1') {
        $where_conditions[] = 's.credit_balance > 0';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM suppliers s WHERE $where_clause";
    $count_stmt = mysqli_prepare($con, $count_sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    }
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    // Get suppliers with statistics
    $sql = "SELECT s.*,
                   COUNT(DISTINCT po.po_id) as total_orders,
                   SUM(CASE WHEN po.status != 'cancelled' THEN po.total_amount ELSE 0 END) as total_amount,
                   MAX(po.order_date) as last_order_date,
                   COUNT(DISTINCT sp.payment_id) as total_payments,
                   SUM(sp.amount) as total_paid
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            LEFT JOIN supplier_payments sp ON s.supplier_id = sp.supplier_id
            WHERE $where_clause
            GROUP BY s.supplier_id
            ORDER BY s.supplier_name
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
    
    $suppliers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['total_amount'] = $row['total_amount'] ?? 0;
        $row['total_paid'] = $row['total_paid'] ?? 0;
        $row['outstanding_amount'] = max(0, $row['total_amount'] - $row['total_paid']);
        $suppliers[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $suppliers,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total_records,
            'total_pages' => ceil($total_records / $limit)
        ]
    ]);
}

/**
 * Get single supplier with detailed information
 */
function getSupplier() {
    global $con;
    
    $supplier_id = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : 0;
    if (!$supplier_id) {
        throw new Exception('Supplier ID is required', 400);
    }
    
    // Get supplier details
    $sql = "SELECT s.*,
                   COUNT(DISTINCT po.po_id) as total_orders,
                   SUM(CASE WHEN po.status != 'cancelled' THEN po.total_amount ELSE 0 END) as total_amount,
                   MAX(po.order_date) as last_order_date,
                   COUNT(DISTINCT sp.payment_id) as total_payments,
                   SUM(sp.amount) as total_paid
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            LEFT JOIN supplier_payments sp ON s.supplier_id = sp.supplier_id
            WHERE s.supplier_id = ?
            GROUP BY s.supplier_id";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $supplier_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$supplier = mysqli_fetch_assoc($result)) {
        throw new Exception('Supplier not found', 404);
    }
    
    $supplier['total_amount'] = $supplier['total_amount'] ?? 0;
    $supplier['total_paid'] = $supplier['total_paid'] ?? 0;
    $supplier['outstanding_amount'] = max(0, $supplier['total_amount'] - $supplier['total_paid']);
    
    // Get recent purchase orders
    $po_sql = "SELECT po.po_id, po.po_number, po.order_date, po.status, po.total_amount,
                      COUNT(DISTINCT grn.grn_id) as grn_count
               FROM purchase_orders po
               LEFT JOIN goods_receipt_notes grn ON po.po_id = grn.po_id
               WHERE po.supplier_id = ?
               GROUP BY po.po_id
               ORDER BY po.order_date DESC
               LIMIT 10";
    
    $po_stmt = mysqli_prepare($con, $po_sql);
    mysqli_stmt_bind_param($po_stmt, 'i', $supplier_id);
    mysqli_stmt_execute($po_stmt);
    $po_result = mysqli_stmt_get_result($po_stmt);
    
    $recent_orders = [];
    while ($po = mysqli_fetch_assoc($po_result)) {
        $recent_orders[] = $po;
    }
    
    // Get recent payments
    $payment_sql = "SELECT payment_id, date, amount, method, reference_no, note, po_id
                   FROM supplier_payments
                   WHERE supplier_id = ?
                   ORDER BY date DESC
                   LIMIT 10";
    
    $payment_stmt = mysqli_prepare($con, $payment_sql);
    mysqli_stmt_bind_param($payment_stmt, 'i', $supplier_id);
    mysqli_stmt_execute($payment_stmt);
    $payment_result = mysqli_stmt_get_result($payment_stmt);
    
    $recent_payments = [];
    while ($payment = mysqli_fetch_assoc($payment_result)) {
        $recent_payments[] = $payment;
    }
    
    $supplier['recent_orders'] = $recent_orders;
    $supplier['recent_payments'] = $recent_payments;
    
    echo json_encode([
        'success' => true,
        'data' => $supplier
    ]);
}

/**
 * Search suppliers for dropdown/autocomplete
 */
function searchSuppliers() {
    global $con;
    
    $search = isset($_GET['q']) ? $_GET['q'] : '';
    $limit = isset($_GET['limit']) ? min(50, intval($_GET['limit'])) : 10;
    
    $sql = "SELECT supplier_id, supplier_name, supplier_tel, credit_balance
            FROM suppliers
            WHERE supplier_name LIKE ? OR supplier_tel LIKE ?
            ORDER BY supplier_name
            LIMIT ?";
    
    $search_param = '%' . $search . '%';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssi', $search_param, $search_param, $limit);
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
 * Get supplier payments with filtering
 */
function getSupplierPayments() {
    global $con;
    
    $supplier_id = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : 0;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $where_conditions = ['1=1'];
    $params = [];
    $types = '';
    
    if ($supplier_id) {
        $where_conditions[] = 'sp.supplier_id = ?';
        $params[] = $supplier_id;
        $types .= 'i';
    }
    
    // Date range filter
    if (!empty($_GET['date_from'])) {
        $where_conditions[] = 'sp.date >= ?';
        $params[] = $_GET['date_from'];
        $types .= 's';
    }
    
    if (!empty($_GET['date_to'])) {
        $where_conditions[] = 'sp.date <= ?';
        $params[] = $_GET['date_to'];
        $types .= 's';
    }
    
    // Payment method filter
    if (!empty($_GET['method'])) {
        $where_conditions[] = 'sp.method = ?';
        $params[] = $_GET['method'];
        $types .= 's';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total 
                  FROM supplier_payments sp 
                  LEFT JOIN suppliers s ON sp.supplier_id = s.supplier_id
                  WHERE $where_clause";
    
    $count_stmt = mysqli_prepare($con, $count_sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    }
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    // Get payments
    $sql = "SELECT sp.*, s.supplier_name, po.po_number,
                   e.name as created_by_name
            FROM supplier_payments sp
            LEFT JOIN suppliers s ON sp.supplier_id = s.supplier_id
            LEFT JOIN purchase_orders po ON sp.po_id = po.po_id
            LEFT JOIN employees e ON sp.created_by = e.employee_id
            WHERE $where_clause
            ORDER BY sp.date DESC
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
    
    $payments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $payments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $payments,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total_records,
            'total_pages' => ceil($total_records / $limit)
        ]
    ]);
}

/**
 * Get outstanding payments summary
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
    $total_outstanding = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['calculated_outstanding'] = max($row['credit_balance'], 
                                           $row['total_pending_amount'] - $row['total_paid']);
        $total_outstanding += $row['calculated_outstanding'];
        $outstanding[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'suppliers' => $outstanding,
            'total_outstanding' => $total_outstanding,
            'count' => count($outstanding)
        ]
    ]);
}

/**
 * Get supplier statistics
 */
function getSupplierStatistics() {
    global $con;
    
    $stats = [];
    
    // Total suppliers
    $sql = "SELECT COUNT(*) as total FROM suppliers";
    $result = mysqli_query($con, $sql);
    $stats['total_suppliers'] = mysqli_fetch_assoc($result)['total'];
    
    // Suppliers with outstanding balance
    $sql = "SELECT COUNT(*) as total FROM suppliers WHERE credit_balance > 0";
    $result = mysqli_query($con, $sql);
    $stats['suppliers_with_outstanding'] = mysqli_fetch_assoc($result)['total'];
    
    // Total outstanding amount
    $sql = "SELECT SUM(credit_balance) as total FROM suppliers WHERE credit_balance > 0";
    $result = mysqli_query($con, $sql);
    $stats['total_outstanding'] = mysqli_fetch_assoc($result)['total'] ?? 0;
    
    // Top suppliers by purchase amount (last 12 months)
    $sql = "SELECT s.supplier_id, s.supplier_name, SUM(po.total_amount) as total_amount
            FROM suppliers s
            INNER JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            WHERE po.order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND po.status != 'cancelled'
            GROUP BY s.supplier_id
            ORDER BY total_amount DESC
            LIMIT 5";
    
    $result = mysqli_query($con, $sql);
    $top_suppliers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $top_suppliers[] = $row;
    }
    $stats['top_suppliers'] = $top_suppliers;
    
    // Monthly purchase trend (last 6 months)
    $sql = "SELECT DATE_FORMAT(po.order_date, '%Y-%m') as month,
                   COUNT(DISTINCT po.po_id) as order_count,
                   SUM(po.total_amount) as total_amount
            FROM purchase_orders po
            WHERE po.order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            AND po.status != 'cancelled'
            GROUP BY DATE_FORMAT(po.order_date, '%Y-%m')
            ORDER BY month";
    
    $result = mysqli_query($con, $sql);
    $monthly_trend = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_trend[] = $row;
    }
    $stats['monthly_trend'] = $monthly_trend;
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}

/**
 * Create new supplier
 */
function createSupplier() {
    global $con;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Invalid JSON data', 400);
    }
    
    // Validate required fields
    $required = ['supplier_name', 'supplier_tel', 'supplier_address'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            throw new Exception("$field is required", 400);
        }
    }
    
    // Check if supplier name already exists
    $check_sql = "SELECT supplier_id FROM suppliers WHERE supplier_name = ?";
    $check_stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 's', $input['supplier_name']);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_fetch_assoc($check_result)) {
        throw new Exception('Supplier name already exists', 400);
    }
    
    // Insert supplier
    $sql = "INSERT INTO suppliers (supplier_name, supplier_tel, supplier_address, credit_balance, note) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssds',
        trim($input['supplier_name']),
        trim($input['supplier_tel']),
        trim($input['supplier_address']),
        isset($input['credit_balance']) ? floatval($input['credit_balance']) : 0,
        isset($input['note']) ? trim($input['note']) : ''
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to create supplier', 500);
    }
    
    $supplier_id = mysqli_insert_id($con);
    
    echo json_encode([
        'success' => true,
        'data' => ['supplier_id' => $supplier_id],
        'message' => 'Supplier created successfully'
    ]);
}

/**
 * Update supplier
 */
function updateSupplier() {
    global $con;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['supplier_id'])) {
        throw new Exception('Supplier ID is required', 400);
    }
    
    $supplier_id = intval($input['supplier_id']);
    
    // Check if supplier exists
    $check_sql = "SELECT supplier_id FROM suppliers WHERE supplier_id = ?";
    $check_stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 'i', $supplier_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (!mysqli_fetch_assoc($check_result)) {
        throw new Exception('Supplier not found', 404);
    }
    
    // Build update query
    $update_fields = [];
    $params = [];
    $types = '';
    
    if (isset($input['supplier_name']) && trim($input['supplier_name']) !== '') {
        $update_fields[] = 'supplier_name = ?';
        $params[] = trim($input['supplier_name']);
        $types .= 's';
    }
    
    if (isset($input['supplier_tel'])) {
        $update_fields[] = 'supplier_tel = ?';
        $params[] = trim($input['supplier_tel']);
        $types .= 's';
    }
    
    if (isset($input['supplier_address'])) {
        $update_fields[] = 'supplier_address = ?';
        $params[] = trim($input['supplier_address']);
        $types .= 's';
    }
    
    if (isset($input['note'])) {
        $update_fields[] = 'note = ?';
        $params[] = trim($input['note']);
        $types .= 's';
    }
    
    if (empty($update_fields)) {
        throw new Exception('No fields to update', 400);
    }
    
    $params[] = $supplier_id;
    $types .= 'i';
    
    $sql = "UPDATE suppliers SET " . implode(', ', $update_fields) . " WHERE supplier_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update supplier', 500);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Supplier updated successfully'
    ]);
}

/**
 * Add supplier payment
 */
function addPayment() {
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
 * Update credit balance
 */
function updateCreditBalance() {
    global $con;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['supplier_id'], $input['credit_balance'])) {
        throw new Exception('Supplier ID and credit balance are required', 400);
    }
    
    $supplier_id = intval($input['supplier_id']);
    $credit_balance = floatval($input['credit_balance']);
    
    if ($credit_balance < 0) {
        throw new Exception('Credit balance cannot be negative', 400);
    }
    
    $sql = "UPDATE suppliers SET credit_balance = ? WHERE supplier_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'di', $credit_balance, $supplier_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update credit balance', 500);
    }
    
    if (mysqli_affected_rows($con) === 0) {
        throw new Exception('Supplier not found', 404);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Credit balance updated successfully'
    ]);
}

/**
 * Delete supplier
 */
function deleteSupplier() {
    global $con;
    
    $supplier_id = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : 0;
    if (!$supplier_id) {
        throw new Exception('Supplier ID is required', 400);
    }
    
    // Check if supplier has any purchase orders
    $check_sql = "SELECT COUNT(*) as count FROM purchase_orders WHERE supplier_id = ?";
    $check_stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 'i', $supplier_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $order_count = mysqli_fetch_assoc($check_result)['count'];
    
    if ($order_count > 0) {
        throw new Exception('Cannot delete supplier with existing purchase orders', 400);
    }
    
    $sql = "DELETE FROM suppliers WHERE supplier_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $supplier_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to delete supplier', 500);
    }
    
    if (mysqli_affected_rows($con) === 0) {
        throw new Exception('Supplier not found', 404);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Supplier deleted successfully'
    ]);
}

?>