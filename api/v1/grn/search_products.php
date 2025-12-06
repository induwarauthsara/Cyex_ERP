<?php
/**
 * Search Products for GRN API
 * 
 * Search products with batch information for adding to GRN
 * GET /api/v1/grn/search_products.php
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
    // Get search parameters
    $search = isset($_GET['q']) ? trim($_GET['q']) : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;
    
    // Get product type filter (default to 'standard')
    $product_type = isset($_GET['type']) ? $_GET['type'] : 'standard';

    // Build query conditions
    $where_conditions = [];
    $params = [];
    $param_types = '';
    
    if (!empty($search)) {
        $where_conditions[] = "(p.product_name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $param_types .= 'sss';
    }
    
    if ($product_type === 'standard') {
        $where_conditions[] = "p.product_type = 'standard'";
    }
    
    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    // Main query to get products with latest batch information
    $sql = "
        SELECT 
            p.product_id,
            p.product_name,
            p.sku,
            p.barcode,
            p.product_type,
            COALESCE(pb.cost, 0) as cost,
            COALESCE(pb.selling_price, 0) as selling_price,
            COALESCE(pb.quantity, 0) as current_stock,
            p.stock_alert_limit
        FROM products p
        LEFT JOIN (
            SELECT 
                product_id,
                cost,
                selling_price,
                quantity
            FROM product_batch
            WHERE batch_id IN (
                SELECT MAX(batch_id)
                FROM product_batch
                GROUP BY product_id
            )
        ) pb ON p.product_id = pb.product_id
        $where_clause
        ORDER BY p.product_name
        LIMIT ? OFFSET ?
    ";

    // Prepare and execute query
    $stmt = mysqli_prepare($con, $sql);
    $params[] = $limit;
    $params[] = $offset;
    $param_types .= 'ii';
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to search products: ' . mysqli_error($con));
    }
    
    $result = mysqli_stmt_get_result($stmt);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            'product_id' => (int)$row['product_id'],
            'product_name' => $row['product_name'],
            'sku' => $row['sku'],
            'barcode' => $row['barcode'],
            'product_type' => $row['product_type'],
            'cost' => (float)$row['cost'],
            'selling_price' => (float)$row['selling_price'],
            'current_stock' => (float)$row['current_stock'],
            'stock_alert_limit' => (float)$row['stock_alert_limit']
        ];
    }

    // Count total for pagination
    $count_params = [];
    $count_param_types = '';
    $count_conditions = [];
    
    if (!empty($search)) {
        $count_conditions[] = "(p.product_name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
        $count_params[] = $search_param;
        $count_params[] = $search_param;
        $count_params[] = $search_param;
        $count_param_types .= 'sss';
    }
    
    if ($product_type === 'standard') {
        $count_conditions[] = "p.product_type = 'standard'";
    }
    
    $count_where = !empty($count_conditions) ? "WHERE " . implode(" AND ", $count_conditions) : "";
    $count_sql = "SELECT COUNT(*) as total FROM products p " . $count_where;
    
    $count_stmt = mysqli_prepare($con, $count_sql);
    if (!empty($count_params)) {
        mysqli_stmt_bind_param($count_stmt, $count_param_types, ...$count_params);
    }
    
    if (!mysqli_stmt_execute($count_stmt)) {
        throw new Exception('Failed to count products: ' . mysqli_error($con));
    }
    
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total = mysqli_fetch_assoc($count_result)['total'];

    ApiResponse::success([
        'products' => $products,
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
