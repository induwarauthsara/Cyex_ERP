<?php
// File: products/API/update_barcode_symbology.php
require_once '../../inc/config.php';
require_once 'detect_barcode_symbology.php';

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Function to update all product barcode symbologies
function updateAllBarcodeSymbologies($con) {
    // Get all products with barcodes
    $productsQuery = "SELECT product_id, barcode, barcode_symbology FROM products WHERE barcode IS NOT NULL AND barcode != ''";
    $result = mysqli_query($con, $productsQuery);
    
    $updated = 0;
    $skipped = 0;
    $failed = 0;
    $errors = [];
    
    while ($product = mysqli_fetch_assoc($result)) {
        $productId = $product['product_id'];
        $barcode = $product['barcode'];
        $currentSymbology = $product['barcode_symbology'];
        
        // Detect the symbology
        $detectedSymbology = detectBarcodeSymbology($barcode);
        
        // If current symbology is empty or different from detected, update it
        if (empty($currentSymbology) || $currentSymbology !== $detectedSymbology) {
            $updateQuery = "UPDATE products SET barcode_symbology = ? WHERE product_id = ?";
            $stmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmt, 'si', $detectedSymbology, $productId);
            
            if (mysqli_stmt_execute($stmt)) {
                $updated++;
            } else {
                $failed++;
                $errors[] = "Failed to update product ID {$productId}: " . mysqli_error($con);
            }
        } else {
            $skipped++;
        }
    }
    
    return [
        'updated' => $updated,
        'skipped' => $skipped,
        'failed' => $failed,
        'errors' => $errors
    ];
}

// Process the request
try {
    // Start transaction
    mysqli_begin_transaction($con);
    
    $result = updateAllBarcodeSymbologies($con);
    
    // Commit transaction
    mysqli_commit($con);
    
    echo json_encode([
        'success' => true,
        'message' => "Barcode symbology update complete. Updated: {$result['updated']}, Skipped: {$result['skipped']}, Failed: {$result['failed']}",
        'details' => $result
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($con);
    
    echo json_encode([
        'success' => false,
        'message' => 'Error updating barcode symbologies: ' . $e->getMessage()
    ]);
} 