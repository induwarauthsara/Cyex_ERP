<?php
/**
 * List One-Time Products API
 * 
 * Get list of one-time products with optional filtering
 * GET /api/v1/one_time_products/list.php
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate user
$user = ApiAuth::requireAuth();

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get query parameters
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
    $status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : 'all';
    $offset = ($page - 1) * $per_page;
    
    // Build status filter
    $status_condition = '';
    if ($status !== 'all') {
        $valid_statuses = ['uncleared', 'cleared', 'skip'];
        if (in_array($status, $valid_statuses)) {
            $status_condition = "WHERE o.status = '$status'";
        }
    }
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total 
                    FROM oneTimeProducts_sales o 
                    $status_condition";
    
    $count_result = mysqli_query($con, $count_query);
    if (!$count_result) {
        throw new Exception("Error counting records: " . mysqli_error($con));
    }
    
    $total = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total / $per_page);
    
    // Get products
    $query = "SELECT 
                o.oneTimeProduct_id as id,
                o.product as product_name,
                o.qty as quantity,
                o.rate,
                o.amount,
                o.status,
                o.invoice_number,
                o.worker
              FROM oneTimeProducts_sales o
              $status_condition
              ORDER BY o.oneTimeProduct_id DESC
              LIMIT $per_page OFFSET $offset";
    
    $result = mysqli_query($con, $query);
    if (!$result) {
        throw new Exception("Error fetching products: " . mysqli_error($con));
    }
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            'id' => (int)$row['id'],
            'product_name' => $row['product_name'],
            'quantity' => (float)$row['quantity'],
            'rate' => (float)$row['rate'],
            'amount' => (float)$row['amount'],
            'status' => $row['status'],
            'invoice_number' => $row['invoice_number'],
            'worker' => $row['worker']
        ];
    }
    
    // Return paginated response
    ApiResponse::paginated($products, $total, $page, $per_page, 'One-time products retrieved successfully');
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve one-time products', 500);
    }
}
