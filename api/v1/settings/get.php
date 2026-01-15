<?php
/**
 * Get Settings API Endpoint
 * Get system settings including employee commission status
 * 
 * @route GET /api/v1/settings/get.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed. Use GET', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Fetch settings
$settings = [];
$keys = [
    'employee_commission_enabled',
    'sell_Insufficient_stock_item',
    'sell_Inactive_batch_products'
];

// Build safe query with placeholders is hard for IN clause without a loop or fixed number
// Since keys are hardcoded above, it is safe to stick them in the query string 
// PROVIDED they are not user input. They are hardcoded here.
$keysString = "'" . implode("','", $keys) . "'";
$query = "SELECT setting_name, setting_value FROM settings WHERE setting_name IN ($keysString)";

$result = mysqli_query($con, $query);

if (!$result) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

// Default values
$responseSettings = [
    'employee_commission_enabled' => false,
    'sell_Insufficient_stock_item' => true,
    'sell_Inactive_batch_products' => true
];

while ($row = mysqli_fetch_assoc($result)) {
    $val = $row['setting_value'];
    
    // Type casting based on known keys
    if (in_array($row['setting_name'], $keys)) {
        // These are boolean/integer flags
        $responseSettings[$row['setting_name']] = (bool)intval($val);
    } else {
        $responseSettings[$row['setting_name']] = $val;
    }
}

ApiResponse::success($responseSettings, 'Settings retrieved successfully');
