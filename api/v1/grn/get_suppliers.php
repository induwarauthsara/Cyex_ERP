<?php
/**
 * Get Suppliers for GRN API
 * 
 * Get all suppliers or search suppliers
 * GET /api/v1/grn/get_suppliers.php
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
    // Get search parameter
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 50;
    $offset = ($page - 1) * $limit;

    // Build query
    $where_clause = '';
    $params = [];
    $param_types = '';
    
    if (!empty($search)) {
        $where_clause = "WHERE supplier_name LIKE ? OR supplier_tel LIKE ? OR supplier_address LIKE ?";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $param_types = 'sss';
    }

    $sql = "
        SELECT 
            supplier_id,
            supplier_name,
            supplier_tel,
            supplier_address,
            credit_balance,
            note
        FROM suppliers
        $where_clause
        ORDER BY supplier_name
        LIMIT ? OFFSET ?
    ";

    $stmt = mysqli_prepare($con, $sql);
    $params[] = $limit;
    $params[] = $offset;
    $param_types .= 'ii';
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to fetch suppliers: ' . mysqli_error($con));
    }

    $result = mysqli_stmt_get_result($stmt);
    $suppliers = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = [
            'supplier_id' => (int)$row['supplier_id'],
            'supplier_name' => $row['supplier_name'],
            'supplier_tel' => $row['supplier_tel'],
            'supplier_address' => $row['supplier_address'],
            'credit_balance' => (float)$row['credit_balance'],
            'note' => $row['note']
        ];
    }

    // Count total
    $count_where = '';
    $count_params = [];
    $count_types = '';
    
    if (!empty($search)) {
        $count_where = "WHERE supplier_name LIKE ? OR supplier_tel LIKE ? OR supplier_address LIKE ?";
        $count_params[] = $search_param;
        $count_params[] = $search_param;
        $count_params[] = $search_param;
        $count_types = 'sss';
    }

    $count_sql = "SELECT COUNT(*) as total FROM suppliers $count_where";
    $count_stmt = mysqli_prepare($con, $count_sql);
    
    if (!empty($count_params)) {
        mysqli_stmt_bind_param($count_stmt, $count_types, ...$count_params);
    }
    
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total = mysqli_fetch_assoc($count_result)['total'];

    ApiResponse::success([
        'suppliers' => $suppliers,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => (int)$total,
            'total_pages' => ceil($total / $limit),
            'has_more' => ($page * $limit) < $total
        ]
    ]);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
