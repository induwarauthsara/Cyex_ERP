<?php
// products/API/process_import.php
require_once '../../inc/config.php';

header('Content-Type: application/json');

// --- Helper Functions ---
function getOrCreateBrand($con, $name) {
    if (empty($name)) return null;
    
    $name = trim($name);
    // Try to find
    $stmt = $con->prepare("SELECT brand_id FROM brands WHERE brand_name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return $row['brand_id'];
    }
    
    // Create
    $stmt = $con->prepare("INSERT INTO brands (brand_name, status) VALUES (?, 1)");
    $stmt->bind_param("s", $name);
    if ($stmt->execute()) {
        return $con->insert_id;
    }
    return null;
}

function getOrCreateCategory($con, $name) {
    if (empty($name)) return null;

    $name = trim($name);
    // Try to find
    $stmt = $con->prepare("SELECT category_id FROM categories WHERE category_name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return $row['category_id'];
    }

    // Create
    $stmt = $con->prepare("INSERT INTO categories (category_name, status) VALUES (?, 1)");
    $stmt->bind_param("s", $name);
    if ($stmt->execute()) {
        return $con->insert_id;
    }
    return null;
}

try {
    // Expect JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['products'])) {
        throw new Exception("Invalid data received.");
    }

    $con->begin_transaction();

    $summary = [
        'added' => 0,
        'failed' => 0,
        'errors' => []
    ];

    foreach ($input['products'] as $prodData) {
        try {
            // 1. Prepare Product Data
            $productName = $prodData['name'];
            $barcode = $prodData['barcode'];
            $sku = $prodData['sku'];
            $catName = $prodData['category'];
            $brandName = $prodData['brand'];

            // 2. Resolve IDs
            $brandId = getOrCreateBrand($con, $brandName);
            $categoryId = getOrCreateCategory($con, $catName);
            
            // Check duplicates (Safety check)
            $check = $con->prepare("SELECT product_id FROM products WHERE barcode = ? OR sku = ?");
            $check->bind_param("ss", $barcode, $sku);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                 // Optimization: Skip or Update? For now, we skip to avoid messing up existing data blindly.
                 throw new Exception("Product with Barcode '$barcode' or SKU '$sku' already exists in the system.");
            }

            // 3. Create Product
            // Detect Symbology
            require_once 'detect_barcode_symbology.php';
            $symbology = detectBarcodeSymbology($barcode);

            // Note: product_type = 'standard'
            // has_stock = 1
            $stmt = $con->prepare("INSERT INTO products 
                (product_name, product_type, barcode, barcode_symbology, sku, category_id, brand_id, has_stock, active_status) 
                VALUES (?, 'standard', ?, ?, ?, ?, ?, 1, 1)");
            
            $stmt->bind_param("ssssii", $productName, $barcode, $symbology, $sku, $categoryId, $brandId);
            
            if (!$stmt->execute()) {
                throw new Exception("DB Error: " . $stmt->error);
            }
            $productId = $con->insert_id;

            // 4. Create Variants (Batches)
            foreach ($prodData['variants'] as $variant) {
                // Determine Batch Number
                // If it's a "Standard" import (no explicit variant name provided), generate a default one like createProduct.php does
                if ($variant['is_default'] && strpos($variant['name'], 'BATCH-') === 0) {
                     // It was already auto-generated in preview_import.php
                     $batchNumber = $variant['name'];
                } else {
                     $batchNumber = $variant['name'];
                }

                // If for some reason batchNumber is still empty, generate one
                if (empty($batchNumber)) {
                    $batchNumber = 'BATCH-' . date('Ymd') . '-' . substr(uniqid(), -5);
                }

                $notes = $variant['note'];
                $cost = $variant['cost'];
                $price = $variant['price'];
                $stock = $variant['stock'];
                $alert = $variant['alert_qty'];
                // Check if discount price exists in the variant data (we need to add this to preview_import first)
                $discountPrice = isset($variant['discount_price']) ? $variant['discount_price'] : null;

                $bStmt = $con->prepare("INSERT INTO product_batch 
                    (product_id, batch_number, cost, selling_price, discount_price, quantity, alert_quantity, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                
                $bStmt->bind_param("isddddds", 
                    $productId, 
                    $batchNumber, 
                    $cost, 
                    $price, 
                    $discountPrice,
                    $stock, 
                    $alert,
                    $notes
                );

                if (!$bStmt->execute()) {
                    throw new Exception("Failed to create variant '$batchNumber': " . $bStmt->error);
                }
            }
            
            $summary['added']++;

        } catch (Exception $e) {
            $summary['failed']++;
            $summary['errors'][] = "Product '{$prodData['name']}': " . $e->getMessage();
        }
    }

    if ($summary['failed'] == 0 && $summary['added'] > 0) {
        $con->commit();
    } else if ($summary['added'] > 0 && $summary['failed'] > 0) {
        // Partial success? 
        // Strategy: Commit the good ones, report the bad ones.
        // Or Rollback all?
        // Usually, in import, "All or Nothing" is safer, OR "Best Effort".
        // Let's do "Best Effort" (Commit successful ones)
        $con->commit();
    } else {
        $con->rollback();
    }

    echo json_encode([
        'success' => true,
        'summary' => $summary
    ]);

} catch (Exception $e) {
    if (isset($con)) $con->rollback();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
