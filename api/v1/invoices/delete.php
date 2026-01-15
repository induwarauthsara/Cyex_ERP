<?php
/**
 * Delete Invoice API Endpoint
 * Handles invoice deletion with stock rollback and commission reversals
 * Supports Dry Run mode for preview
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../../../inc/config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';
require_once __DIR__ . '/../services/InvoiceService.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed. Use POST', 405);
}

// Authenticate User
$user_data = ApiAuth::authenticate();
if (!$user_data['success']) {
    ApiResponse::unauthorized($user_data['message']);
}

// Get Request Data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['invoice_number']) || !isset($data['reason'])) {
    ApiResponse::validationError(['invoice_number' => 'Required', 'reason' => 'Required']);
}

$invoice_number = intval($data['invoice_number']);
$reason = trim($data['reason']);
$restock = isset($data['restock_items']) ? (bool)$data['restock_items'] : true;
$dry_run = isset($data['preview_only']) ? (bool)$data['preview_only'] : false;

try {
    $service = new InvoiceService($con, $user_data['user']['id'], $user_data['user']['name']);
    
    $result = $service->deleteInvoice($invoice_number, $reason, $restock, $dry_run);
    
    if ($dry_run) {
        ApiResponse::success($result, 'Invoice deletion preview generated.');
    } else {
        ApiResponse::success($result, 'Invoice deleted successfully.');
    }

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
