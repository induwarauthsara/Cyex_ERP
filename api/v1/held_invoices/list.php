<?php
/**
 * List Held Invoices API
 * 
 * Get list of held invoices
 * GET /api/v1/held_invoices/list.php
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
    $status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : 'held';
    $offset = ($page - 1) * $per_page;
    
    // Build status filter
    $status_condition = "WHERE h.status = 'held'";
    if ($status === 'all') {
        $status_condition = '';
    }
    
    // Check if table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'held_invoices'");
    if (mysqli_num_rows($table_check) === 0) {
        // Return empty list if table doesn't exist yet
        ApiResponse::paginated([], 0, 1, $per_page, 'No held invoices found');
        exit;
    }
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total FROM held_invoices h $status_condition";
    $count_result = mysqli_query($con, $count_query);
    if (!$count_result) {
        throw new Exception("Error counting records: " . mysqli_error($con));
    }
    
    $total = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total / $per_page);
    
    // Get held invoices
    $query = "SELECT 
                h.id,
                h.customer_name,
                h.customer_number,
                h.items,
                h.total_amount,
                h.discount_amount,
                h.discount_type,
                h.discount_value,
                h.total_payable,
                h.status,
                h.held_at,
                h.individual_discount_mode
              FROM held_invoices h
              $status_condition
              ORDER BY h.id DESC
              LIMIT $per_page OFFSET $offset";
    
    $result = mysqli_query($con, $query);
    if (!$result) {
        throw new Exception("Error fetching held invoices: " . mysqli_error($con));
    }
    
    $held_invoices = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items = json_decode($row['items'], true);
        
        $held_invoices[] = [
            'id' => (int)$row['id'],
            'customer' => [
                'name' => $row['customer_name'],
                'number' => $row['customer_number']
            ],
            'items' => $items,
            'item_count' => is_array($items) ? count($items) : 0,
            'total_amount' => (float)$row['total_amount'],
            'discount_amount' => (float)$row['discount_amount'],
            'discount' => [
                'type' => $row['discount_type'],
                'value' => (float)$row['discount_value']
            ],
            'total_payable' => (float)$row['total_payable'],
            'status' => $row['status'],
            'held_at' => $row['held_at'],
            'individual_discount_mode' => (bool)$row['individual_discount_mode']
        ];
    }
    
    // Return paginated response
    ApiResponse::paginated($held_invoices, $total, $page, $per_page, 'Held invoices retrieved successfully');
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve held invoices', 500);
    }
}
