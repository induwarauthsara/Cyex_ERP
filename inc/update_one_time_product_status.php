<?php
require_once './config.php';

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$product_id = $data['product_id'];
$status = $data['status'];

// Validate status
$valid_statuses = ['uncleared', 'cleared', 'skip'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $stmt = $con->prepare("UPDATE oneTimeProducts_sales SET status = ? WHERE oneTimeProduct_id = ?");
    $stmt->bind_param("si", $status, $product_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$con->close();
?> 