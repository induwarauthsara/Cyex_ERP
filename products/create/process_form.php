<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $productName = $_POST['productName'];
    $productCode = $_POST['productCode'];
    $slugSKU = $_POST['slugSKU'];
    $barcodeSymbology = $_POST['barcodeSymbology'];
    $brand = $_POST['brand'];
    $initialStock = $_POST['initialStock'];
    $category = $_POST['category'];
    $showInEcommerce = isset($_POST['showInEcommerce']) ? 1 : 0;
    $cost = $_POST['cost'];
    $sellingPrice = $_POST['sellingPrice'];
    $supplier = $_POST['supplier'];
    $promotion = isset($_POST['promotion']) ? 1 : 0;
    $alertQuantity = $_POST['alertQuantity'];

    // Handle image upload
    $productImage = NULL;
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $productImage = file_get_contents($_FILES['productImage']['tmp_name']);
    }

    // Insert data into the database
    $conn = new mysqli('localhost', 'username', 'password', 'pos_system');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO products (product_name, product_code, slug_sku, barcode_symbology, brand, initial_stock, category, show_in_ecommerce, cost, selling_price, supplier, promotion, alert_quantity, product_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiisddsb", $productName, $productCode, $slugSKU, $barcodeSymbology, $brand, $initialStock, $category, $showInEcommerce, $cost, $sellingPrice, $supplier, $promotion, $alertQuantity, $productImage);

    if ($stmt->execute()) {
        echo "<script>Swal.fire('Success', 'Product has been created!', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error', 'Something went wrong. Please try again.', 'error');</script>";
    }

    $stmt->close();
    $conn->close();
}
