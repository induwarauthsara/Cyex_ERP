<?php
/**
 * Stock Summary API
 * 
 * Get stock/inventory summary
 * GET /api/v1/stock/summary.php
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
    // Get overall statistics
    $stats_query = "SELECT 
                      COUNT(DISTINCT p.product_id) as total_products,
                      COALESCE(SUM(pb.quantity), 0) as total_stock_qty,
                      COALESCE(SUM(pb.quantity * pb.cost), 0) as total_stock_value
                    FROM products p
                    LEFT JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
                    WHERE p.has_stock = '1' AND p.active_status = 1";
    
    $stats_result = mysqli_query($con, $stats_query);
    $stats = mysqli_fetch_assoc($stats_result);
    
    // Get low stock items
    $low_stock_query = "SELECT 
                          p.product_id,
                          p.product_name,
                          pb.quantity as current_stock,
                          pb.alert_quantity,
                          pb.cost
                        FROM products p
                        INNER JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
                        WHERE p.has_stock = '1' 
                        AND p.active_status = 1
                        AND pb.quantity <= pb.alert_quantity
                        AND pb.quantity > 0
                        ORDER BY (pb.quantity / NULLIF(pb.alert_quantity, 0)) ASC
                        LIMIT 20";
    
    $low_stock_result = mysqli_query($con, $low_stock_query);
    
    $low_stock_items = [];
    while ($row = mysqli_fetch_assoc($low_stock_result)) {
        $low_stock_items[] = [
            'product_id' => (int)$row['product_id'],
            'product_name' => $row['product_name'],
            'current_stock' => (float)$row['current_stock'],
            'alert_level' => (float)$row['alert_quantity'],
            'stock_level_percentage' => $row['alert_quantity'] > 0 
                ? round(($row['current_stock'] / $row['alert_quantity']) * 100, 2) 
                : 0,
            'cost_price' => (float)$row['cost'],
            'status' => 'low_stock'
        ];
    }
    
    // Get out of stock items
    $out_of_stock_query = "SELECT 
                             p.product_id,
                             p.product_name,
                             pb.alert_quantity
                           FROM products p
                           INNER JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
                           WHERE p.has_stock = '1' 
                           AND p.active_status = 1
                           AND pb.quantity <= 0
                           LIMIT 20";
    
    $out_of_stock_result = mysqli_query($con, $out_of_stock_query);
    
    $out_of_stock_items = [];
    while ($row = mysqli_fetch_assoc($out_of_stock_result)) {
        $out_of_stock_items[] = [
            'product_id' => (int)$row['product_id'],
            'product_name' => $row['product_name'],
            'alert_level' => (float)$row['alert_quantity'],
            'status' => 'out_of_stock'
        ];
    }
    
    // Get stock by category
    $category_query = "SELECT 
                         c.category_name,
                         COUNT(DISTINCT p.product_id) as product_count,
                         COALESCE(SUM(pb.quantity), 0) as total_qty,
                         COALESCE(SUM(pb.quantity * pb.cost), 0) as total_value
                       FROM products p
                       LEFT JOIN categories c ON p.category_id = c.category_id
                       LEFT JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
                       WHERE p.has_stock = '1' AND p.active_status = 1
                       GROUP BY c.category_name
                       ORDER BY total_value DESC";
    
    $category_result = mysqli_query($con, $category_query);
    
    $stock_by_category = [];
    while ($row = mysqli_fetch_assoc($category_result)) {
        $stock_by_category[] = [
            'category' => $row['category_name'] ?? 'Uncategorized',
            'product_count' => (int)$row['product_count'],
            'total_quantity' => (float)$row['total_qty'],
            'total_value' => (float)$row['total_value']
        ];
    }
    
    // Return stock summary
    ApiResponse::success('Stock summary retrieved successfully', [
        'overview' => [
            'total_products' => (int)$stats['total_products'],
            'total_stock_quantity' => (float)$stats['total_stock_qty'],
            'total_stock_value' => (float)$stats['total_stock_value'],
            'low_stock_count' => count($low_stock_items),
            'out_of_stock_count' => count($out_of_stock_items)
        ],
        'low_stock_items' => $low_stock_items,
        'out_of_stock_items' => $out_of_stock_items,
        'stock_by_category' => $stock_by_category
    ]);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve stock summary', 500);
    }
}
