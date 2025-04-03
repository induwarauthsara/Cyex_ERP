<?php
// updateProduct.php
require_once '../../inc/config.php';

header('Content-Type: application/json');

class ProductException extends Exception
{
    private $field;

    public function __construct($message, $field = '')
    {
        parent::__construct($message);
        $this->field = $field;
    }

    public function getField()
    {
        return $this->field;
    }
}

function validateProductData($productData, $productId)
{
    if (empty($productData['productName'])) {
        throw new ProductException("Product name is required", "productName");
    }

    if (empty($productData['productCode'])) {
        throw new ProductException("Product code/barcode is required", "productCode");
    }

    if (empty($productData['sku'])) {
        throw new ProductException("SKU is required", "sku");
    }

    // Validate product type specific fields
    switch ($productData['productType']) {
        case 'standard':
            if (!isset($productData['hasVariant']) || !$productData['hasVariant']) {
                if (isset($productData['cost']) && isset($productData['sellingPrice'])) {
                    if ($productData['cost'] <= 0) {
                        throw new ProductException("Valid cost is required", "cost");
                    }
                    if ($productData['sellingPrice'] <= 0) {
                        throw new ProductException("Valid selling price is required", "sellingPrice");
                    }
                    if ($productData['sellingPrice'] < $productData['cost']) {
                        throw new ProductException("Selling price cannot be less than cost", "sellingPrice");
                    }
                }
            }
            break;

        case 'combo':
            if (empty($productData['comboProducts'])) {
                throw new ProductException("Combo product must contain at least one component", "comboProducts");
            }
            break;
    }

    // Validate variants if present
    if (isset($productData['hasVariant']) && $productData['hasVariant'] && isset($productData['variants'])) {
        if (empty($productData['variants'])) {
            throw new ProductException("At least one variant is required", "variants");
        }
        foreach ($productData['variants'] as $index => $variant) {
            if (empty($variant['variantName'])) {
                throw new ProductException("Variant name is required for variant #" . ($index + 1), "variant-name");
            }
            // Make variant value optional
            // if (empty($variant['variantValue'])) {
            //     throw new ProductException("Variant value is required for variant #" . ($index + 1), "variant-value");
            // }
            if ($variant['variantCost'] <= 0) {
                throw new ProductException("Valid cost is required for variant #" . ($index + 1), "variant-cost");
            }
            if ($variant['variantPrice'] <= 0) {
                throw new ProductException("Valid price is required for variant #" . ($index + 1), "variant-price");
            }
            if ($variant['variantPrice'] < $variant['variantCost']) {
                throw new ProductException("Selling price cannot be less than cost for variant #" . ($index + 1), "variant-price");
            }
        }
    }
}

function validateImage($file)
{
    if (!empty($file['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new ProductException("Invalid image type. Only JPG, PNG and GIF are allowed", "productImage");
        }

        if ($file['size'] > $maxSize) {
            throw new ProductException("Image size should not exceed 5MB", "productImage");
        }
    }
}

function checkDuplicates($con, $productData, $productId)
{
    // Check duplicate SKU
    $stmt = $con->prepare("SELECT product_id FROM products WHERE sku = ? AND product_id != ? LIMIT 1");
    $stmt->bind_param("si", $productData['sku'], $productId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new ProductException("This SKU is already in use by another product", "sku");
    }

    // Check duplicate barcode
    $stmt = $con->prepare("SELECT product_id FROM products WHERE barcode = ? AND product_id != ? LIMIT 1");
    $stmt->bind_param("si", $productData['productCode'], $productId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new ProductException("This barcode is already in use by another product", "productCode");
    }

    // Check duplicate product name
    $stmt = $con->prepare("SELECT product_id FROM products WHERE product_name = ? AND product_id != ? LIMIT 1");
    $stmt->bind_param("si", $productData['productName'], $productId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new ProductException("This product name is already in use by another product", "productName");
    }

    // Check duplicate batch_number for new variants
    if (isset($productData['hasVariant']) && $productData['hasVariant'] && isset($productData['variants'])) {
        foreach ($productData['variants'] as $variant) {
            if (isset($variant['isNew']) && $variant['isNew']) {
                $batchNumber = $variant['variantName'];
                $stmt = $con->prepare("SELECT product_id FROM product_batch WHERE batch_number = ? LIMIT 1");
                $stmt->bind_param("s", $batchNumber);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    throw new ProductException("Batch number '" . $batchNumber . "' is already in use. Please use a unique batch number for each variant.", "variants");
                }
            }
        }
    }
}

try {
    if (!isset($_POST['productData']) || !isset($_POST['productId'])) {
        throw new ProductException("Missing required data");
    }

    // Get and decode the product data
    $productData = json_decode($_POST['productData'], true);
    $productId = (int)$_POST['productId'];
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new ProductException("Invalid JSON data received");
    }
    
    // Debug log
    error_log('Product data received: ' . print_r($productData, true));
    
    // Validate product ID
    $stmt = $con->prepare("SELECT product_id, product_type, image FROM products WHERE product_id = ? LIMIT 1");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new ProductException("Product not found");
    }
    
    $existingProduct = $result->fetch_assoc();
    $originalImage = $existingProduct['image'];
    $originalType = $existingProduct['product_type'];

    // Validate product data
    validateProductData($productData, $productId);

    // Validate image if uploaded
    if (isset($_FILES['productImage'])) {
        validateImage($_FILES['productImage']);
    }

    // Start transaction
    $con->begin_transaction();

    // Check for duplicates
    checkDuplicates($con, $productData, $productId);

    // Prepare base product data
    $productType = $con->real_escape_string($productData['productType']);
    $productName = $con->real_escape_string($productData['productName']);
    $productCode = $con->real_escape_string($productData['productCode']);
    $barcodeSymbology = $con->real_escape_string($productData['barcodeSymbology'] ?? 'CODE128');
    $sku = $con->real_escape_string($productData['sku']);
    $showInEcommerce = $productData['showInEcommerce'] ? '1' : '0';
    $categoryId = !empty($productData['categoryId']) ? (int)$productData['categoryId'] : null;
    $brandId = !empty($productData['brandId']) ? (int)$productData['brandId'] : null;
    $activeStatus = isset($productData['activeStatus']) ? ($productData['activeStatus'] ? '1' : '0') : '1';

    // Handle image upload
    $imagePath = $originalImage;
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === 0) {
        $uploadDir = '../../uploads/products/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new ProductException("Failed to create upload directory");
            }
        }

        $imageFileName = uniqid() . '_' . basename($_FILES['productImage']['name']);
        $targetPath = $uploadDir . $imageFileName;

        if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $targetPath)) {
            throw new ProductException("Failed to upload image", "productImage");
        }
        
        // Delete old image if exists and is not the default
        if ($originalImage && $originalImage !== 'no-image.png' && file_exists($uploadDir . $originalImage)) {
            unlink($uploadDir . $originalImage);
        }
        
        $imagePath = $imageFileName;
    }

    // Update products table
    $sql = "UPDATE products SET 
            product_name = ?, 
            product_type = ?, 
            barcode = ?, 
            barcode_symbology = ?, 
            sku = ?, 
            show_in_landing_page = ?, 
            category_id = ?, 
            brand_id = ?, 
            active_status = ?, 
            image = ? 
            WHERE product_id = ?";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new ProductException("Database error: " . $con->error);
    }

    $stmt->bind_param(
        "sssssiiiisi",
        $productName,
        $productType,
        $productCode,
        $barcodeSymbology,
        $sku,
        $showInEcommerce,
        $categoryId,
        $brandId,
        $activeStatus,
        $imagePath,
        $productId
    );

    if (!$stmt->execute()) {
        throw new ProductException("Error updating product: " . $stmt->error);
    }

    // Handle product type specific updates
    switch ($productType) {
        case 'standard':
            if (isset($productData['hasVariant']) && $productData['hasVariant'] && isset($productData['variants'])) {
                // Handle variants - update existing and add new ones
                foreach ($productData['variants'] as $variant) {
                    // Debug log for variant data
                    error_log('Processing variant: ' . print_r($variant, true));
                    
                    $batchNumber = $variant['variantName'];
                    $variantValue = $variant['variantValue'];
                    $cost = $variant['variantCost'];
                    $price = $variant['variantPrice'];
                    $quantity = $variant['variantQuantity'];
                    $alertQty = $variant['variantAlertQty'];
                    $discountPrice = !empty($variant['variantDiscountPrice']) ? $variant['variantDiscountPrice'] : null;
                    
                    // Debug log for discount price
                    error_log('Discount price: ' . var_export($discountPrice, true));
                    
                    if (isset($variant['batchId']) && $variant['batchId'] > 0) {
                        // Update existing variant
                        $sql = "UPDATE product_batch SET 
                                batch_number = ?, 
                                cost = ?, 
                                selling_price = ?, 
                                discount_price = ?, 
                                quantity = ?, 
                                notes = ?, 
                                alert_quantity = ? 
                                WHERE batch_id = ? AND product_id = ?";
                        
                        $stmt = $con->prepare($sql);
                        if (!$stmt) {
                            throw new ProductException("Database error: " . $con->error);
                        }
                        
                        $batchId = $variant['batchId'];
                        $stmt->bind_param(
                            "sdddissii",
                            $batchNumber,
                            $cost,
                            $price,
                            $discountPrice,
                            $quantity,
                            $variantValue,
                            $alertQty,
                            $batchId,
                            $productId
                        );
                    } else {
                        // Add new variant
                        $sql = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, discount_price, quantity, notes, alert_quantity) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        
                        $stmt = $con->prepare($sql);
                        if (!$stmt) {
                            throw new ProductException("Database error: " . $con->error);
                        }
                        
                        $stmt->bind_param(
                            "isdddisi",
                            $productId,
                            $batchNumber,
                            $cost,
                            $price,
                            $discountPrice,
                            $quantity,
                            $variantValue,
                            $alertQty
                        );
                    }
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error updating variant: " . $stmt->error);
                    }
                }
                
                // Delete removed variants if batchesToDelete is provided
                if (isset($productData['batchesToDelete']) && !empty($productData['batchesToDelete'])) {
                    $batchIdsToDelete = implode(',', array_map('intval', $productData['batchesToDelete']));
                    $sql = "DELETE FROM product_batch WHERE batch_id IN ({$batchIdsToDelete}) AND product_id = ?";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }
                    
                    $stmt->bind_param("i", $productId);
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error deleting variants: " . $stmt->error);
                    }
                }
            } elseif (!isset($productData['hasVariant']) || !$productData['hasVariant']) {
                // Single variant/batch update
                if (isset($productData['batchId']) && $productData['batchId'] > 0) {
                    // Update existing batch
                    $discountPrice = !empty($productData['discountPrice']) ? $productData['discountPrice'] : null;
                    $alertQuantity = isset($productData['alertQuantity']) ? intval($productData['alertQuantity']) : 5;
                    
                    $sql = "UPDATE product_batch SET 
                            cost = ?, 
                            selling_price = ?, 
                            discount_price = ?, 
                            quantity = ?, 
                            alert_quantity = ? 
                            WHERE batch_id = ? AND product_id = ?";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }
                    
                    $batchId = $productData['batchId'];
                    $stmt->bind_param(
                        "dddiiii",
                        $productData['cost'],
                        $productData['sellingPrice'],
                        $discountPrice,
                        $productData['initialStock'],
                        $alertQuantity,
                        $batchId,
                        $productId
                    );
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error updating batch: " . $stmt->error);
                    }
                } else {
                    // Create a new batch
                    $batchNumber = 'BATCH-' . date('Ymd') . '-' . substr(uniqid(), -5);
                    $discountPrice = !empty($productData['discountPrice']) ? $productData['discountPrice'] : null;
                    $alertQuantity = isset($productData['alertQuantity']) ? intval($productData['alertQuantity']) : 5;
                    
                    $sql = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, discount_price, quantity, alert_quantity) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }
                    
                    $stmt->bind_param(
                        "isdddii",
                        $productId,
                        $batchNumber,
                        $productData['cost'],
                        $productData['sellingPrice'],
                        $discountPrice,
                        $productData['initialStock'],
                        $alertQuantity
                    );
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error creating batch: " . $stmt->error);
                    }
                }
            }
            break;

        case 'combo':
            // Handle combo products
            if (isset($productData['comboProducts']) && !empty($productData['comboProducts'])) {
                // Delete existing combo components
                $sql = "DELETE FROM combo_products WHERE combo_product_id = ?";
                $stmt = $con->prepare($sql);
                if (!$stmt) {
                    throw new ProductException("Database error: " . $con->error);
                }
                
                $stmt->bind_param("i", $productId);
                
                if (!$stmt->execute()) {
                    throw new ProductException("Error removing existing combo components: " . $stmt->error);
                }
                
                // Create combo_products table if not exists
                $sql = "CREATE TABLE IF NOT EXISTS combo_products (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    combo_product_id INT,
                    component_product_id INT,
                    quantity INT,
                    batch_number VARCHAR(50),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (combo_product_id) REFERENCES products(product_id),
                    FOREIGN KEY (component_product_id) REFERENCES products(product_id)
                )";
                
                if (!$con->query($sql)) {
                    throw new ProductException("Error creating combo_products table: " . $con->error);
                }
                
                // Get current batch number
                $batchQuery = "SELECT batch_number FROM product_batch WHERE product_id = ? LIMIT 1";
                $stmt = $con->prepare($batchQuery);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $batchResult = $stmt->get_result();
                
                if ($batchResult->num_rows > 0) {
                    $batchNumber = $batchResult->fetch_assoc()['batch_number'];
                } else {
                    // Create a new batch if none exists
                    $batchNumber = 'BATCH-' . date('Ymd') . '-' . substr(uniqid(), -5);
                    $sql = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, quantity) 
                            VALUES (?, ?, ?, ?, 0)";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }
                    
                    $cost = $productData['cost'] ?? 0;
                    $sellingPrice = $productData['sellingPrice'] ?? 0;
                    
                    $stmt->bind_param(
                        "isdd",
                        $productId,
                        $batchNumber,
                        $cost,
                        $sellingPrice
                    );
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error creating combo batch: " . $stmt->error);
                    }
                }
                
                // Insert new combo components
                foreach ($productData['comboProducts'] as $component) {
                    $sql = "INSERT INTO combo_products (combo_product_id, component_product_id, quantity, batch_number) 
                            VALUES (?, ?, ?, ?)";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }
                    
                    $stmt->bind_param(
                        "iiis",
                        $productId,
                        $component['productId'],
                        $component['quantity'],
                        $batchNumber
                    );
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error creating combo component: " . $stmt->error);
                    }
                }
            }
            break;

        case 'digital':
        case 'service':
            // Update batch for digital/service product if needed
            $batchQuery = "SELECT batch_id FROM product_batch WHERE product_id = ? LIMIT 1";
            $stmt = $con->prepare($batchQuery);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $batchResult = $stmt->get_result();
            
            if (isset($productData['cost']) && isset($productData['sellingPrice'])) {
                if ($batchResult->num_rows > 0) {
                    // Update existing batch
                    $batchId = $batchResult->fetch_assoc()['batch_id'];
                    
                    $sql = "UPDATE product_batch SET 
                            cost = ?, 
                            selling_price = ? 
                            WHERE batch_id = ?";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }
                    
                    $stmt->bind_param(
                        "ddi",
                        $productData['cost'],
                        $productData['sellingPrice'],
                        $batchId
                    );
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error updating digital/service batch: " . $stmt->error);
                    }
                } else {
                    // Create a new batch
                    $batchNumber = 'BATCH-' . date('Ymd') . '-' . substr(uniqid(), -5);
                    
                    $sql = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, quantity) 
                            VALUES (?, ?, ?, ?, 0)";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }
                    
                    $stmt->bind_param(
                        "isdd",
                        $productId,
                        $batchNumber,
                        $productData['cost'],
                        $productData['sellingPrice']
                    );
                    
                    if (!$stmt->execute()) {
                        throw new ProductException("Error creating digital/service batch: " . $stmt->error);
                    }
                }
            }
            break;
    }

    // Commit transaction
    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Product updated successfully',
        'productId' => $productId
    ]);
} catch (ProductException $e) {
    // Rollback transaction on error
    if (isset($con) && $con->ping()) {
        $con->rollback();
    }

    // Delete uploaded image if exists and error occurred
    if (isset($targetPath) && file_exists($targetPath)) {
        unlink($targetPath);
    }

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'field' => $e->getField(),
        'type' => 'validation'
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con) && $con->ping()) {
        $con->rollback();
    }

    // Delete uploaded image if exists and error occurred
    if (isset($targetPath) && file_exists($targetPath)) {
        unlink($targetPath);
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later.',
        'type' => 'system',
        'debug' => $e->getMessage() // Remove in production
    ]);
}

// Close connection
if (isset($con)) {
    $con->close();
}
