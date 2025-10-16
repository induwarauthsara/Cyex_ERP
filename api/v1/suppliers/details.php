<?php
/**
 * Supplier Details API
 * 
 * Get detailed information about a supplier
 * GET /api/v1/suppliers/details.php?id=1
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
    // Get supplier ID
    if (!isset($_GET['id'])) {
        ApiResponse::error('Supplier ID is required', 422);
    }
    
    $supplier_id = intval($_GET['id']);
    
    // Get supplier details
    $query = "SELECT 
                supplier_id as id,
                supplier_name as name,
                supplier_tel as mobile,
                supplier_address as address,
                credit_balance,
                created_at
              FROM suppliers
              WHERE supplier_id = ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $supplier_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        ApiResponse::error('Supplier not found', 404);
    }
    
    $supplier = mysqli_fetch_assoc($result);
    
    // Get purchase statistics (using goods_receipt_notes)
    $stats_query = "SELECT 
                      COUNT(*) as total_purchases,
                      COALESCE(SUM(total_amount), 0) as total_amount
                    FROM goods_receipt_notes
                    WHERE supplier_id = ?";
    
    $stmt = mysqli_prepare($con, $stats_query);
    mysqli_stmt_bind_param($stmt, 'i', $supplier_id);
    mysqli_stmt_execute($stmt);
    $stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    // Get recent purchases
    $purchases_query = "SELECT 
                          grn_id as purchase_id,
                          total_amount as total,
                          paid_amount as paid,
                          outstanding_amount as balance,
                          receipt_date as date,
                          payment_status as status
                        FROM goods_receipt_notes
                        WHERE supplier_id = ?
                        ORDER BY receipt_date DESC
                        LIMIT 10";
    
    $stmt = mysqli_prepare($con, $purchases_query);
    mysqli_stmt_bind_param($stmt, 'i', $supplier_id);
    mysqli_stmt_execute($stmt);
    $purchases_result = mysqli_stmt_get_result($stmt);
    
    $recent_purchases = [];
    while ($row = mysqli_fetch_assoc($purchases_result)) {
        $recent_purchases[] = [
            'purchase_id' => (int)$row['purchase_id'],
            'total' => (float)$row['total'],
            'paid' => (float)$row['paid'],
            'balance' => (float)$row['balance'],
            'date' => $row['date'],
            'status' => $row['status']
        ];
    }
    
    // Return supplier details
    ApiResponse::success('Supplier details retrieved successfully', [
        'id' => (int)$supplier['id'],
        'name' => $supplier['name'],
        'mobile' => $supplier['mobile'],
        'address' => $supplier['address'],
        'credit_balance' => (float)$supplier['credit_balance'],
        'created_at' => $supplier['created_at'],
        'statistics' => [
            'total_purchases' => (int)$stats['total_purchases'],
            'total_amount' => (float)$stats['total_amount']
        ],
        'recent_purchases' => $recent_purchases
    ]);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve supplier details', 500);
    }
}
