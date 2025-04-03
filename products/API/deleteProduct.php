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
    
    // Check for dependencies
    $hasDependencies = false;
    $dependencyDetails = [];
    
    // Check purchase_order_items
    $sql = "SELECT COUNT(*) as count FROM purchase_order_items WHERE product_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        $hasDependencies = true;
        $dependencyDetails[] = "purchase orders";
    }
    
    // Check for other potential dependencies
    $checkTables = [
        'invoice_items' => 'invoices',
        'order_items' => 'orders',
        'sale_items' => 'sales'
    ];
    
    foreach ($checkTables as $itemTable => $parentTable) {
        $checkSql = "SELECT COUNT(*) as count FROM {$itemTable} WHERE product_id = ?";
        try {
            $checkStmt = $con->prepare($checkSql);
            if ($checkStmt) {
                $checkStmt->bind_param("i", $productId);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result()->fetch_assoc();
                if ($checkResult['count'] > 0) {
                    $hasDependencies = true;
                    $dependencyDetails[] = $parentTable;
                }
            }
        } catch (Exception $ex) {
            // Table might not exist, just continue
        }
    }
    
    // Start transaction
    $con->begin_transaction();
    
    if ($hasDependencies) {
        // If product has dependencies, mark it as inactive instead of deleting
        $sql = "UPDATE products SET active_status = 0 WHERE product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $productId);
        
        if (!$stmt->execute()) {
            throw new Exception("Error marking product as inactive: " . $stmt->error);
        }
        
        // Commit transaction
        $con->commit();
        
        $dependenciesText = count($dependencyDetails) > 0 ? 
            " because it is referenced in " . implode(", ", $dependencyDetails) : "";
            
        echo json_encode([
            'success' => true,
            'message' => "Product cannot be deleted" . $dependenciesText . ". It has been marked as inactive instead.",
            'action' => 'deactivated'
        ]);
    } else {
        // If no dependencies, proceed with deletion
        
        // First delete combo products if any
        $sql = "DELETE FROM combo_products WHERE combo_product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $productId);
        
        if (!$stmt->execute()) {
            throw new Exception("Error deleting combo products: " . $stmt->error);
        }
    
        // Delete product batches
        $sql = "DELETE FROM product_batch WHERE product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $productId);
        
        if (!$stmt->execute()) {
            throw new Exception("Error deleting product batches: " . $stmt->error);
        }
    
        // Finally delete the product
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $productId);
        
        if (!$stmt->execute()) {
            // If there's a foreign key constraint error (which shouldn't happen at this point)
            if ($con->errno == 1451) {
                // Fallback to marking as inactive
                $sql = "UPDATE products SET active_status = 0 WHERE product_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $productId);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error marking product as inactive: " . $stmt->error);
                }
                
                // Commit transaction
                $con->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => "Product couldn't be deleted due to database constraints. It has been marked as inactive instead.",
                    'action' => 'deactivated'
                ]);
                exit;
            }
            throw new Exception("Error deleting product: " . $stmt->error);
        }
    
        // Commit transaction
        $con->commit();
    
        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully',
            'action' => 'deleted'
        ]);
    }

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