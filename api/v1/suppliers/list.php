<?php
/**
 * List Suppliers API
 * 
 * Get list of suppliers
 * GET /api/v1/suppliers/list.php
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
    $offset = ($page - 1) * $per_page;
    
    // Build search filter
    $search_condition = '';
    if (!empty($search)) {
        $search_condition = "WHERE supplier_name LIKE '%$search%' OR supplier_tel LIKE '%$search%' OR supplier_address LIKE '%$search%'";
    }
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total FROM suppliers $search_condition";
    $count_result = mysqli_query($con, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total / $per_page);
    
    // Get suppliers
    $query = "SELECT 
                supplier_id as id,
                supplier_name as name,
                supplier_tel as mobile,
                supplier_address as address,
                credit_balance,
                created_at
              FROM suppliers
              $search_condition
              ORDER BY supplier_name ASC
              LIMIT $per_page OFFSET $offset";
    
    $result = mysqli_query($con, $query);
    if (!$result) {
        throw new Exception("Error fetching suppliers: " . mysqli_error($con));
    }
    
    $suppliers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'mobile' => $row['mobile'],
            'address' => $row['address'],
            'credit_balance' => (float)$row['credit_balance'],
            'created_at' => $row['created_at']
        ];
    }
    
    // Return paginated response
    ApiResponse::paginated($suppliers, $total, $page, $per_page, 'Suppliers retrieved successfully');
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve suppliers', 500);
    }
}
