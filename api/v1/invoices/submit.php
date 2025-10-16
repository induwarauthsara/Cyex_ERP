<?php
/**
 * Submit Invoice API Endpoint
 * Process and save invoice/transaction
 * This is an API wrapper for the existing submit-invoice.php
 * 
 * @route POST /api/v1/invoices/submit.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed. Use POST', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Create session for backward compatibility with existing submit-invoice.php
ApiAuth::createSession($userData);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if ($input === null) {
    ApiResponse::error('Invalid JSON data received', 400);
}

// Add fallback biller ID from authenticated user
if (!isset($input['fallbackBillerId'])) {
    $input['fallbackBillerId'] = $userData['employee_id'];
}

// Forward to existing invoice submission logic
// We'll include the file and let it handle the processing
// But we'll catch the output and format it as API response

ob_start();

// Set the input data for submit-invoice.php to read
$_POST = [];
$_REQUEST = [];
file_put_contents('php://input', json_encode($input));

// Include the existing submission logic
include __DIR__ . '/../../../submit-invoice.php';

$output = ob_get_clean();

// Try to decode the output as JSON
$result = json_decode($output, true);

if ($result === null) {
    // If output is not JSON, treat as error
    ApiResponse::serverError('Invoice processing failed', $output);
}

// Return the result maintaining the existing response format
if (isset($result['success']) && $result['success']) {
    ApiResponse::success(
        isset($result['data']) ? $result['data'] : $result,
        isset($result['message']) ? $result['message'] : 'Invoice submitted successfully',
        201
    );
} else {
    ApiResponse::error(
        isset($result['message']) ? $result['message'] : 'Invoice submission failed',
        400,
        isset($result['errors']) ? $result['errors'] : []
    );
}
