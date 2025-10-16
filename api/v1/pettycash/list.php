<?php
/**
 * Petty Cash List API
 * 
 * Get petty cash transactions
 * GET /api/v1/pettycash/list.php
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
    $date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : null;
    $date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : null;
    $offset = ($page - 1) * $per_page;
    
    // Build date filter
    $date_condition = '';
    if ($date_from && $date_to) {
        $date_condition = "WHERE date BETWEEN '$date_from' AND '$date_to'";
    } elseif ($date_from) {
        $date_condition = "WHERE date >= '$date_from'";
    } elseif ($date_to) {
        $date_condition = "WHERE date <= '$date_to'";
    }
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total FROM pettycash $date_condition";
    $count_result = mysqli_query($con, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total / $per_page);
    
    // Get petty cash records
    $query = "SELECT 
                id,
                perrycash as description,
                amount,
                date,
                time,
                emp_name as employee_name
              FROM pettycash
              $date_condition
              ORDER BY date DESC, time DESC
              LIMIT $per_page OFFSET $offset";
    
    $result = mysqli_query($con, $query);
    if (!$result) {
        throw new Exception("Error fetching petty cash: " . mysqli_error($con));
    }
    
    // Get company name from config
    global $ERP_COMPANY_NAME;
    
    $petty_cash = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $emp_name = $row['employee_name'];
        if (empty($emp_name)) {
            $emp_name = $ERP_COMPANY_NAME ?? 'Company';
        }
        
        $petty_cash[] = [
            'id' => (int)$row['id'],
            'description' => $row['description'],
            'amount' => (float)$row['amount'],
            'date' => $row['date'],
            'time' => $row['time'],
            'employee_name' => $emp_name
        ];
    }
    
    // Calculate total amount
    $total_amount_query = "SELECT COALESCE(SUM(amount), 0) as total_amount FROM pettycash $date_condition";
    $total_amount_result = mysqli_query($con, $total_amount_query);
    $total_amount = mysqli_fetch_assoc($total_amount_result)['total_amount'];
    
    // Return paginated response with additional summary
    $response = [
        'success' => true,
        'message' => 'Petty cash records retrieved successfully',
        'data' => $petty_cash,
        'meta' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => 'v1',
            'pagination' => [
                'total' => (int)$total,
                'per_page' => $per_page,
                'current_page' => $page,
                'total_pages' => $total_pages,
                'has_more' => $page < $total_pages
            ],
            'summary' => [
                'total_amount' => (float)$total_amount,
                'date_range' => [
                    'from' => $date_from,
                    'to' => $date_to
                ]
            ]
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve petty cash records', 500);
    }
}
