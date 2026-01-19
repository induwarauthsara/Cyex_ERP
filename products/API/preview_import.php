<?php
// products/API/preview_import.php
require_once '../../inc/config.php';

header('Content-Type: application/json');

function generateBarcode($con) {
    // Simple logic to generate a unique 8-digit barcode
    // You might want to match the logic in createProduct exactly
    do {
        $barcode = mt_rand(10000000, 99999999);
        $stmt = $con->prepare("SELECT product_id FROM products WHERE barcode = ?");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $res = $stmt->get_result();
    } while ($res->num_rows > 0);
    
    return (string)$barcode;
}

try {
    if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Please upload a valid CSV file.");
    }

    $file = fopen($_FILES['csvFile']['tmp_name'], 'r');
    if (!$file) {
        throw new Exception("Could not open the CSV file.");
    }

    // Read header row
    $header = fgetcsv($file);
    if (!$header) {
        throw new Exception("The CSV file is empty.");
    }

    // Normalize headers: lowercase, trim, remove invisible chars
    $headerMap = [];
    foreach ($header as $index => $colName) {
        // Remove BOM if present
        $cleanName = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $colName);
        $cleanName = strtolower(trim($cleanName));
        $headerMap[$cleanName] = $index;
    }

    // Required columns
    $required = ['product name', 'cost', 'selling price']; 
    // Barcode and SKU are conceptually required but can be generated/defaulted
    // Based on user request: "if barcode is empty, Generate Barcode"

    foreach ($required as $req) {
        if (!isset($headerMap[$req])) {
            throw new Exception("Missing required column: " . ucfirst($req));
        }
    }

    $products = []; // Keyed by normalized Product Name
    $rowNumber = 1; // Start from 1 (header is 0)

    while (($row = fgetcsv($file)) !== false) {
        $rowNumber++;
        // Skip empty rows
        if (empty(array_filter($row))) continue;

        $pName = trim($row[$headerMap['product name']] ?? '');
        if (empty($pName)) continue;

        // --- Extract Values ---
        $barcode = trim($row[$headerMap['barcode']] ?? '');
        $sku = trim($row[$headerMap['sku']] ?? '');
        
        $variantName = trim($row[$headerMap['variant name']] ?? '');
        $variantNote = trim($row[$headerMap['variant note']] ?? '');
        
        $cost = floatval($row[$headerMap['cost']] ?? 0);
        $sellingPrice = floatval($row[$headerMap['selling price']] ?? 0);
        // Added Discount Price
        $discountPrice = isset($headerMap['discount price']) ? floatval($row[$headerMap['discount price']] ?? 0) : 0;
        // If discount price is 0, we treat it as null/no discount effectively later, or store 0. Database allows NULL usually, but float column stores 0.00
        
        $stock = floatval($row[$headerMap['stock']] ?? 0);
        $alertQty = floatval($row[$headerMap['alert qty']] ?? 5);
        
        $catName = trim($row[$headerMap['category']] ?? '');
        $brandName = trim($row[$headerMap['brand']] ?? '');

        // --- Logic: Group by Product Name ---
        // We use a temporary key to group rows.
        $key = strtolower($pName);

        if (!isset($products[$key])) {
            // New Product
            
            // Auto-generate Barcode if empty
            if (empty($barcode)) {
                $barcode = generateBarcode($con);
                $generatedBarcode = true;
            } else {
                $generatedBarcode = false;
            }

            // Auto-generate SKU if empty (Fallback: P-{Barcode})
            if (empty($sku)) {
                $sku = "SKU-" . $barcode;
            }

            $products[$key] = [
                'name' => $pName,
                'barcode' => $barcode,
                'sku' => $sku,
                'category' => $catName,
                'brand' => $brandName,
                'is_barcode_generated' => $generatedBarcode,
                'variants' => []
            ];
        } else {
            // Existing Product - Validation
            // Ensure Barcode matches (optional strictness, for now we assume the first row's barcode is master)
            // If the user provided a DIFFERENT barcode for a variant of the same product, we ignore it or could flag a warning.
            // For now, we force variants to share the product's barcode/sku from the first row found.
        }

        // --- Add Variant/Batch ---
        
        // If Variant Name is empty, generate one
        if (empty($variantName)) {
            // If it's a "standard" product (one row), we usually don't show a variant name,
            // but internally it is a batch.
            $variantName = "BATCH-" . date('Ymd') . "-" . substr(uniqid(), -5);
            $isDefaultBatch = true;
        } else {
            $isDefaultBatch = false;
        }

        $products[$key]['variants'][] = [
            'name' => $variantName,
            'note' => $variantNote,
            'cost' => $cost,
            'price' => $sellingPrice,
            'discount_price' => $discountPrice, // Pass to process_import
            'stock' => $stock,
            'alert_qty' => $alertQty,
            'is_default' => $isDefaultBatch
        ];
    }

    fclose($file);

    echo json_encode([
        'success' => true,
        'products' => array_values($products) // Convert associative array to indexed list
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
