<?php
require_once 'config.php'; // Database configuration and connection

header('Content-Type: application/json'); // Set content type to JSON for response

$search = $_GET['search'] ?? ''; // Get the search term from the AJAX request
if (empty($search)) {
    echo json_encode(['products' => [], 'batches' => []]); // Return empty arrays if no search term is provided
    exit;
}

try {
    // Fetch necessary fields for POS from the products table, with category and brand names
    $productQuery = "
        SELECT 
            p.product_id,
            p.product_name,
            p.description,
            p.stock_qty,
            p.rate,
            p.barcode,
            p.sku,
            c.category_name,
            b.brand_name
        FROM 
            product_view AS p
        LEFT JOIN 
            categories AS c ON p.category_id = c.category_id
        LEFT JOIN 
            brands AS b ON p.brand_id = b.brand_id
        WHERE 
            p.product_name LIKE ? OR p.barcode = ? OR p.sku = ?
    ";

    $stmtProduct = $con->prepare($productQuery);
    $search_param = "%$search%"; // Prepare for partial match in product_name
    $stmtProduct->bind_param("sss", $search_param, $search, $search);
    $stmtProduct->execute();
    $productResult = $stmtProduct->get_result();

    $products = [];
    while ($row = $productResult->fetch_assoc()) {
        $products[] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'description' => $row['description'],
            'stock_qty' => $row['stock_qty'],
            'rate' => $row['rate'],
            'barcode' => $row['barcode'],
            'sku' => $row['sku'],
            'category_name' => $row['category_name'],
            'brand_name' => $row['brand_name']
        ];
    }

    // Fetch relevant batch details for each product if available
    $batchQuery = "
        SELECT 
            pb.batch_id,
            pb.product_id,
            pb.batch_number,
            pb.selling_price,
            pb.expiry_date,
            pb.quantity AS batch_quantity,
            pb.status,
            pb.notes,
            pb.restocked_at,
            s.supplier_name
        FROM 
            product_batch AS pb
        LEFT JOIN 
            suppliers AS s ON pb.supplier_id = s.supplier_id
        WHERE 
            pb.product_id IN (SELECT product_id FROM products WHERE product_name LIKE ? OR barcode = ? OR sku = ?)
    ";

    $stmtBatch = $con->prepare($batchQuery);
    $stmtBatch->bind_param("sss", $search_param, $search, $search);
    $stmtBatch->execute();
    $batchResult = $stmtBatch->get_result();

    $batches = [];
    while ($row = $batchResult->fetch_assoc()) {
        $batches[] = [
            'batch_id' => $row['batch_id'],
            'product_id' => $row['product_id'],
            'batch_number' => $row['batch_number'],
            'selling_price' => $row['selling_price'],
            'expiry_date' => $row['expiry_date'],
            'batch_quantity' => $row['batch_quantity'],
            'status' => $row['status'],
            'notes' => $row['notes'],
            'restocked_at' => $row['restocked_at'],
            'supplier_name' => $row['supplier_name']
        ];
    }

    // Return the products and batches as separate arrays in JSON
    echo json_encode(['products' => $products, 'batches' => $batches]);
} catch (Exception $e) {
    // Handle any errors, such as database connection issues
    echo json_encode(['error' => 'An error occurred while fetching product details.']);
    error_log($e->getMessage()); // Log the error for debugging
}
