<?php
/**
 * Update Invoice API Endpoint
 * Handles invoice updates (Smart Diff) with stock and commission adjustments
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

if (!isset($data['invoice_number']) || !isset($data['items'])) {
    ApiResponse::validationError(['invoice_number' => 'Required', 'items' => 'Required']);
}

$invoice_number = intval($data['invoice_number']);
$items = $data['items']; // Array of items
$discount = isset($data['discount']) ? floatval($data['discount']) : 0;
$reason = (isset($data['reason']) && trim($data['reason']) !== '') ? trim($data['reason']) : 'edit';
$restock_removed = isset($data['restock_items']) ? (bool)$data['restock_items'] : true;
$update_commission = isset($data['update_commission']) ? (bool)$data['update_commission'] : true;
$dry_run = isset($data['preview_only']) ? (bool)$data['preview_only'] : false;

try {
    $biller_name = isset($data['biller']) ? trim($data['biller']) : null;
    $worker_name = $biller_name; // One invoice has only one employee (Biller = Worker)

    $service = new InvoiceService($con, $user_data['user']['id'], $user_data['user']['name']);
    
    $result = $service->updateInvoice($invoice_number, $items, $discount, $reason, $restock_removed, $dry_run, $biller_name, $worker_name, $update_commission);
    
    if ($dry_run) {
        ApiResponse::success($result, 'Invoice update preview generated.');
    } else {
        ApiResponse::success($result, 'Invoice updated successfully.');
    }

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
