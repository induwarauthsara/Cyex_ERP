<?php
require_once '../../inc/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    if (!isset($_POST['productId'])) {
        throw new Exception("Product ID is required");
    }

    $productId = (int)$_POST['productId'];
    $newStatus = isset($_POST['status']) ? (int)$_POST['status'] : null;
    
    // Start transaction
    $con->begin_transaction();
    
    // If status is provided, set to that status, otherwise toggle current status
    if ($newStatus !== null) {
        $sql = "UPDATE products SET active_status = ? WHERE product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $newStatus, $productId);
    } else {
        $sql = "UPDATE products SET active_status = !active_status WHERE product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $productId);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error updating product status: " . $stmt->error);
    }
    
    // Get current status after update
    $sql = "SELECT active_status FROM products WHERE product_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $currentStatus = $row['active_status'];
    
    // Commit transaction
    $con->commit();
    
    $statusText = $currentStatus ? "activated" : "deactivated";
    
    echo json_encode([
        'success' => true,
        'message' => "Product successfully " . $statusText,
        'status' => (int)$currentStatus
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con) && $con->ping()) {
        $con->rollback();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($con)) {
    $con->close();
} 