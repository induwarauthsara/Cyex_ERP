<?php
/**
 * Customers API for AdminPanel
 * Simple endpoint to provide customer list for dropdowns
 */

require_once('../../inc/config.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use GET'
    ]);
    exit;
}

try {
    $customers = [];
    
    // Try to get customers from invoice table first
    $query = "SELECT DISTINCT customer_name as name, customer_mobile 
              FROM invoice 
              WHERE customer_name != '' AND customer_name IS NOT NULL 
              ORDER BY customer_name ASC";
    
    $result = @mysqli_query($con, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = [
                'name' => $row['name'],
                'mobile' => $row['customer_mobile'] ? strval($row['customer_mobile']) : '',
                'address' => '' // Invoice table doesn't have address
            ];
        }
    }
    
    // Also get customers from quotations table
    $quotationQuery = "SELECT DISTINCT customer_name as name, customer_mobile as mobile, 
                       customer_address as address 
                       FROM quotations 
                       WHERE customer_name != '' AND customer_name IS NOT NULL 
                       ORDER BY customer_name ASC";
    
    $quotationResult = @mysqli_query($con, $quotationQuery);
    
    if ($quotationResult) {
        while ($row = mysqli_fetch_assoc($quotationResult)) {
            // Avoid duplicates - update if exists, add if new
            $found = false;
            foreach ($customers as &$existing) {
                if ($existing['name'] === $row['name']) {
                    // Update with quotation data if available
                    if (!empty($row['mobile'])) {
                        $existing['mobile'] = $row['mobile'];
                    }
                    if (!empty($row['address'])) {
                        $existing['address'] = $row['address'];
                    }
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $customers[] = [
                    'name' => $row['name'],
                    'mobile' => $row['mobile'] ?? '',
                    'address' => $row['address'] ?? ''
                ];
            }
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $customers,
        'count' => count($customers)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

?>