<?php
// This file handles one-time product operations for the POS system
require_once 'config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if ($data === null) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Required fields
$productName = $data['productName'] ?? '';
$regularPrice = floatval($data['regularPrice'] ?? 0);
$discountPrice = isset($data['discountPrice']) && !empty($data['discountPrice']) ? 
                 floatval($data['discountPrice']) : $regularPrice;
$quantity = floatval($data['quantity'] ?? 1);

// Validate data
if (empty($productName) || $regularPrice <= 0 || $quantity <= 0) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required fields or invalid values'
    ]);
    exit;
}

// Generate a unique SKU for the one-time product
$sku = 'OTP-' . strtoupper(substr(md5(uniqid()), 0, 8));

// Create a temporary product response
$product = [
    'name' => $productName,
    'regular_price' => $regularPrice,
    'discount_price' => $discountPrice,
    'quantity' => $quantity,
    'isOneTimeProduct' => true,
    'sku' => $sku,
    // Generate a temporary batch ID
    'batch_id' => 'TEMP-' . uniqid()
];

// Return success response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'product' => $product,
    'message' => 'One-time product created successfully'
]); 