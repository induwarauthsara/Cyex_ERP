<?php
/**
 * List GRN (Goods Received Notes) API
 * 
 * Get paginated list of GRN records with filtering options
 * GET /api/v1/grn/list.php
 * 
 * Admin only
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate and require admin role
$user = ApiAuth::requireAuth();
if ($user['employee_role'] !== 'Admin') {
    ApiResponse::forbidden('Admin access required');
}

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get query parameters
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
    $search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
    $supplier_id = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : null;
    $status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : null;
    $payment_status = isset($_GET['payment_status']) ? mysqli_real_escape_string($con, $_GET['payment_status']) : null;
    $date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : null;
    $date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : null;
    $offset = ($page - 1) * $per_page;
    
    // Build WHERE clauses
    $where_clauses = [];
    
    // Exclude cancelled GRNs by default (unless specifically filtered)
    if (empty($status) || $status !== 'cancelled') {
        $where_clauses[] = "grn.status != 'cancelled'";
    }
    
    // Search filter
    if (!empty($search)) {
        $where_clauses[] = "(grn.grn_number LIKE '%$search%' OR grn.invoice_number LIKE '%$search%' OR s.supplier_name LIKE '%$search%' OR po.po_number LIKE '%$search%')";
    }
    
    // Supplier filter
    if ($supplier_id !== null && $supplier_id > 0) {
        $where_clauses[] = "grn.supplier_id = $supplier_id";
    }
    
    // Status filter
    if (!empty($status) && in_array($status, ['draft', 'completed', 'cancelled'])) {
        $where_clauses[] = "grn.status = '$status'";
    }
    
    // Payment status filter
    if (!empty($payment_status) && in_array($payment_status, ['paid', 'partial', 'unpaid'])) {
        $where_clauses[] = "grn.payment_status = '$payment_status'";
    }
    
    // Date range filter
    if (!empty($date_from)) {
        $where_clauses[] = "grn.receipt_date >= '$date_from'";
    }
    if (!empty($date_to)) {
        $where_clauses[] = "grn.receipt_date <= '$date_to'";
    }
    
    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total 
                    FROM goods_receipt_notes grn
                    LEFT JOIN suppliers s ON grn.supplier_id = s.supplier_id
                    LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
                    $where_sql";
    $count_result = mysqli_query($con, $count_query);
    
    if (!$count_result) {
        throw new Exception('Failed to count records: ' . mysqli_error($con));
    }
    
    $total = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total / $per_page);
    
    // Get GRN list with details
    $query = "SELECT 
                grn.grn_id,
                grn.grn_number,
                grn.receipt_date,
                grn.invoice_number,
                grn.invoice_date,
                grn.status,
                grn.total_amount,
                grn.paid_amount,
                grn.outstanding_amount,
                grn.payment_status,
                grn.payment_method,
                grn.notes,
                grn.created_at,
                po.po_number,
                po.po_id,
                s.supplier_id,
                s.supplier_name,
                s.supplier_tel,
                e.emp_name as created_by_name,
                (SELECT COUNT(*) FROM grn_items WHERE grn_id = grn.grn_id) as item_count
              FROM goods_receipt_notes grn
              LEFT JOIN suppliers s ON grn.supplier_id = s.supplier_id
              LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
              LEFT JOIN employees e ON grn.created_by = e.employ_id
              $where_sql
              ORDER BY grn.receipt_date DESC, grn.grn_id DESC
              LIMIT $per_page OFFSET $offset";
    
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception('Failed to fetch GRN list: ' . mysqli_error($con));
    }
    
    $grn_list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $grn_list[] = [
            'id' => (int)$row['grn_id'],
            'grn_number' => $row['grn_number'],
            'receipt_date' => $row['receipt_date'],
            'invoice_number' => $row['invoice_number'],
            'invoice_date' => $row['invoice_date'],
            'status' => $row['status'],
            'total_amount' => (float)$row['total_amount'],
            'paid_amount' => (float)$row['paid_amount'],
            'outstanding_amount' => (float)$row['outstanding_amount'],
            'payment_status' => $row['payment_status'],
            'payment_method' => $row['payment_method'],
            'notes' => $row['notes'],
            'created_at' => $row['created_at'],
            'po_id' => $row['po_id'] ? (int)$row['po_id'] : null,
            'po_number' => $row['po_number'],
            'supplier' => [
                'id' => (int)$row['supplier_id'],
                'name' => $row['supplier_name'],
                'mobile' => $row['supplier_tel']
            ],
            'created_by' => $row['created_by_name'],
            'item_count' => (int)$row['item_count']
        ];
    }
    
    // Prepare metadata
    $meta = [
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => 'v1',
        'pagination' => [
            'total' => (int)$total,
            'per_page' => $per_page,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'has_more' => $page < $total_pages
        ]
    ];
    
    ApiResponse::success($grn_list, 'GRN list retrieved successfully', 200, $meta);
    
} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
