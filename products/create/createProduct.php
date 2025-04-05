<?php
// createProduct.php
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

function generateBatchNumber()
{
    return 'BATCH-' . date('Ymd') . '-' . substr(uniqid(), -5);
}

function validateProductData($productData)
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
                if (!isset($productData['cost']) || $productData['cost'] <= 0) {
                    throw new ProductException("Valid cost is required", "cost");
                }
                if (!isset($productData['sellingPrice']) || $productData['sellingPrice'] <= 0) {
                    throw new ProductException("Valid selling price is required", "sellingPrice");
                }
                if ($productData['sellingPrice'] < $productData['cost']) {
                    throw new ProductException("Selling price cannot be less than cost", "sellingPrice");
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
    if (isset($productData['hasVariant']) && $productData['hasVariant']) {
        if (empty($productData['variants'])) {
            throw new ProductException("At least one variant is required", "variants");
        }
        foreach ($productData['variants'] as $index => $variant) {
            if (empty($variant['variantName'])) {
                throw new ProductException("Variant name is required for variant #" . ($index + 1), "variant-name");
            }
            if (empty($variant['variantValue'])) {
                throw new ProductException("Variant value is required for variant #" . ($index + 1), "variant-value");
            }
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

function checkDuplicates($con, $productData)
{
    // Check duplicate SKU
    $stmt = $con->prepare("SELECT product_id FROM products WHERE sku = ? LIMIT 1");
    $stmt->bind_param("s", $productData['sku']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new ProductException("This SKU is already in use", "sku");
    }

    // Check duplicate barcode
    $stmt = $con->prepare("SELECT product_id FROM products WHERE barcode = ? LIMIT 1");
    $stmt->bind_param("s", $productData['productCode']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new ProductException("This barcode is already in use", "productCode");
    }

    // Check duplicate product name
    $stmt = $con->prepare("SELECT product_id FROM products WHERE product_name = ? LIMIT 1");
    $stmt->bind_param("s", $productData['productName']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new ProductException("This product name is already in use", "productName");
    }

    // check duplicate batch_number
    if ($productData['hasVariant']) {
        foreach ($productData['variants'] as $variant) {
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

try {
    if (!isset($_POST['productData'])) {
        throw new ProductException("No product data received");
    }

    // Get and decode the product data
    $productData = json_decode($_POST['productData'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new ProductException("Invalid JSON data received");
    }

    // Validate product data
    validateProductData($productData);

    // Validate image if uploaded
    if (isset($_FILES['productImage'])) {
        validateImage($_FILES['productImage']);
    }

    // Start transaction
    $con->begin_transaction();

    // Check for duplicates
    checkDuplicates($con, $productData);

    // Prepare base product data
    $productType = $con->real_escape_string($productData['productType']);
    $productName = $con->real_escape_string($productData['productName']);
    $productCode = $con->real_escape_string($productData['productCode']);
    $sku = $con->real_escape_string($productData['sku']);
    $showInEcommerce = $productData['showInEcommerce'] ? '1' : '0';
    $categoryId = !empty($productData['categoryId']) ? (int)$productData['categoryId'] : null;
    $brandId = !empty($productData['brandId']) ? (int)$productData['brandId'] : null;
    
    // Determine barcode symbology if not provided
    if (empty($productData['barcodeSymbology'])) {
        require_once '../API/detect_barcode_symbology.php';
        $barcodeSymbology = detectBarcodeSymbology($productCode);
    } else {
        $barcodeSymbology = $con->real_escape_string($productData['barcodeSymbology']);
    }

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === 0) {
        $uploadDir = 'uploads/products/';
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
        $imagePath = $imageFileName;
    }

    // Insert into products table
    $sql = "INSERT INTO products (product_name, product_type, barcode, barcode_symbology, sku, show_in_landing_page, 
            category_id, brand_id, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new ProductException("Database error: " . $con->error);
    }

    $stmt->bind_param(
        "sssssiiis",
        $productName,
        $productType,
        $productCode,
        $barcodeSymbology,
        $sku,
        $showInEcommerce,
        $categoryId,
        $brandId,
        $imagePath
    );

    if (!$stmt->execute()) {
        throw new ProductException("Error creating product: " . $stmt->error);
    }

    $productId = $con->insert_id;

    // Handle different product types
    switch ($productType) {
        case 'standard':
            if ($productData['hasVariant']) {
                // Handle variants
                foreach ($productData['variants'] as $variant) {
                    $batchNumber = $variant['variantName'];
                    $discountPrice = !empty($variant['variantDiscountPrice']) ? $variant['variantDiscountPrice'] : null;
                    
                    $sql = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, discount_price, quantity, notes, alert_quantity) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }

                    $variantName = $variant['variantValue'];
                    $stmt->bind_param(
                        "isdddisi",
                        $productId,
                        $batchNumber,
                        $variant['variantCost'],
                        $variant['variantPrice'],
                        $discountPrice,
                        $variant['variantQuantity'],
                        $variantName,
                        $variant['variantAlertQty']
                    );

                    if (!$stmt->execute()) {
                        throw new ProductException("Error creating variant: " . $stmt->error);
                    }
                }
            } else {
                // Single variant/batch
                $batchNumber = generateBatchNumber();
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
            break;

        case 'combo':
            // Create main combo batch
            if ($productData['hasVariant']) {
                // Handle variants
                foreach ($productData['variants'] as $variant) {
                    $batchNumber = $variant['variantName'];
                    $sql = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, 
                            quantity, notes) VALUES (?, ?, ?, ?, ?, ?)";

                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }

                    $variantName = $variant['variantValue'];
                    $stmt->bind_param(
                        "isddis",
                        $productId,
                        $batchNumber,
                        $variant['variantCost'],
                        $variant['variantPrice'],
                        $variant['variantStock'],
                        $variantName
                    );

                    if (!$stmt->execute()) {
                        throw new ProductException("Error creating variant: " . $stmt->error);
                    }
                }
            } else {
                // Single variant/batch
                $batchNumber = generateBatchNumber();
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
                    throw new ProductException("Error creating combo batch: " . $stmt->error);
                }
            }

            $comboBatchId = $con->insert_id;

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

            // Insert combo components
            foreach ($productData['comboProducts'] as $component) {
                $sql = "INSERT INTO combo_products (combo_product_id, component_product_id, 
                        quantity, batch_number) VALUES (?, ?, ?, ?)";

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
            break;

        case 'digital':
        case 'service':
            // Create a batch without quantity
            if ($productData['hasVariant']) {
                // Handle variants
                foreach ($productData['variants'] as $variant) {
                    $batchNumber = $variant['variantName'];
                    $sql = "INSERT INTO product_batch (product_id, batch_number, cost, selling_price, 
                            quantity, notes) VALUES (?, ?, ?, ?, ?, ?)";

                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new ProductException("Database error: " . $con->error);
                    }

                    $variantName = $variant['variantValue'];
                    $stmt->bind_param(
                        "isddis",
                        $productId,
                        $batchNumber,
                        $variant['variantCost'],
                        $variant['variantPrice'],
                        $variant['variantStock'],
                        $variantName
                    );

                    if (!$stmt->execute()) {
                        throw new ProductException("Error creating variant: " . $stmt->error);
                    }
                }
            } else {
                // Single variant/batch
                $batchNumber = generateBatchNumber();
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
            break;
    }

    // Commit transaction
    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Product created successfully',
        'productId' => $productId
    ]);
} catch (ProductException $e) {
    // Rollback transaction on error
    if (isset($con) && $con->ping()) {
        $con->rollback();
    }

    // Delete uploaded image if exists
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

    // Delete uploaded image if exists
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
