<?php
require '../../../inc/config.php';


// Check if it's a POST request and if product data is set
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productData'])) {
    $productData = json_decode($_POST['productData'], true);


    $productType = mysqli_real_escape_string($con, $productData['productType']);
    $productName = mysqli_real_escape_string($con, $productData['productName']);
    $productCode = mysqli_real_escape_string($con, $productData['productCode']);
    $barcodeSymbology = mysqli_real_escape_string($con, $productData['barcodeSymbology']);
    $brandId = isset($productData['brandId']) && $productData['brandId'] !== '' ? mysqli_real_escape_string($con, $productData['brandId']) : null;
    $categoryId = isset($productData['categoryId']) && $productData['categoryId'] !== '' ? mysqli_real_escape_string($con, $productData['categoryId']) : null;
    $sku = mysqli_real_escape_string($con, $productData['sku']);
    $showInEcommerce = $productData['showInEcommerce'] ? 1 : 0;
    $hasVariant = $productData['hasVariant'] ? 1 : 0;

    // Insert product main details into products table
    $productInsertQuery = "INSERT INTO products (product_name, barcode, barcode_symbology, brand_id, category_id, sku, show_in_landing_page)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $productInsertQuery);
    mysqli_stmt_bind_param($stmt, "sssiiis", $productName, $productCode, $barcodeSymbology, $brandId, $categoryId, $sku, $showInEcommerce);
    $productInsertResult = mysqli_stmt_execute($stmt);

    if ($productInsertResult) {
        $productId = mysqli_insert_id($con);
        $batchInsert = false;
        //Handle product batch data for standard and combo product
        if ($productType === 'standard') {
            $defaultUnit = mysqli_real_escape_string($con, $productData['defaultUnit']);
            $saleUnit = mysqli_real_escape_string($con, $productData['saleUnit']);
            $purchaseUnit = mysqli_real_escape_string($con, $productData['purchaseUnit']);
            $pcsPerBox = isset($productData['pcsPerBox']) ?  mysqli_real_escape_string($con, $productData['pcsPerBox']) : 1;
            $initialStock =  mysqli_real_escape_string($con, $productData['initialStock']);
            $cost = mysqli_real_escape_string($con, $productData['cost']);
            $sellingPrice =  mysqli_real_escape_string($con, $productData['sellingPrice']);
            // insert into product batch table
            $batchInsertQuery = "INSERT INTO product_batch (product_id, cost, selling_price, quantity)
                       VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $batchInsertQuery);
            mysqli_stmt_bind_param($stmt, "isdd", $productId, $cost, $sellingPrice, $initialStock);
            $batchInsert = mysqli_stmt_execute($stmt);
        } else if ($productType === 'combo') {
            $cost = mysqli_real_escape_string($con, $productData['cost']);
            $sellingPrice = mysqli_real_escape_string($con, $productData['sellingPrice']);

            $batchInsertQuery = "INSERT INTO product_batch (product_id, cost, selling_price, quantity)
                       VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $batchInsertQuery);
            $initialStock = 0;
            mysqli_stmt_bind_param($stmt, "isdd", $productId, $cost, $sellingPrice, $initialStock);
            $batchInsert = mysqli_stmt_execute($stmt);

            // Insert into combo products table
            if (isset($productData['comboProducts'])) {
                foreach ($productData['comboProducts'] as $comboProduct) {
                    $comboProductId = mysqli_real_escape_string($con, $comboProduct['productId']);
                    $comboProductQty = mysqli_real_escape_string($con, $comboProduct['quantity']);
                    $comboInsertQuery = "INSERT INTO combo_products (product_id, combo_product_id, quantity) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($con, $comboInsertQuery);
                    mysqli_stmt_bind_param($stmt, 'iii', $productId, $comboProductId, $comboProductQty);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
        //Handle variant data
        if ($hasVariant && isset($productData['variants'])) {
            foreach ($productData['variants'] as $variant) {
                $variantName = mysqli_real_escape_string($con, $variant['variantName']);
                $variantValue = mysqli_real_escape_string($con, $variant['variantValue']);
                $variantStock = mysqli_real_escape_string($con, $variant['variantStock']);
                $variantCost = mysqli_real_escape_string($con, $variant['variantCost']);
                $variantPrice = mysqli_real_escape_string($con, $variant['variantPrice']);
                $variantAlert = mysqli_real_escape_string($con, $variant['variantAlert']);
                $variantInsertQuery = "INSERT INTO product_variants (product_id, variant_name, variant_value, initial_stock, cost, selling_price, alert_quantity ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $variantInsertQuery);
                mysqli_stmt_bind_param($stmt, "isiddii", $productId, $variantName, $variantValue, $variantStock, $variantCost, $variantPrice, $variantAlert);
                mysqli_stmt_execute($stmt);
            }
        }
        // Handle image upload
        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/'; // Set your upload directory
            $uploadFile = $uploadDir . basename($_FILES['productImage']['name']);
            if (move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadFile)) {
                $imagePath =  basename($_FILES['productImage']['name']);
                $imageUpdateQuery = "UPDATE products SET image = ? WHERE product_id = ?";
                $stmt = mysqli_prepare($con, $imageUpdateQuery);
                mysqli_stmt_bind_param($stmt, "si",  $imagePath,  $productId);
                mysqli_stmt_execute($stmt);
            } else {
                echo json_encode(['error' => 'Error upload the image']);
            }
        }

        if ($productType === 'digital' || $productType === 'service') {
            $batchInsert = true;
        }
        if ($batchInsert) {
            echo json_encode(['success' => true, 'message' => 'Product created successfully']);
        } else {
            echo json_encode(['error' => 'Error adding product batch or combo products.']);
        }
    } else {
        echo json_encode(['error' => 'Error adding product main details.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}

mysqli_close($con);
