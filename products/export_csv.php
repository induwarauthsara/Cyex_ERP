<?php
require_once '../inc/config.php';

// Authentication
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['employee_name'])) {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d_H-i-s') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Filter parameters
$filterCategory = isset($_GET['category']) ? $_GET['category'] : '';
$filterBrand = isset($_GET['brand']) ? $_GET['brand'] : '';
$filterType = isset($_GET['type']) ? $_GET['type'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

// CSV Headers
// CSV Headers
fputcsv($output, [
    'ID', 
    'Product Name', 
    'Batch Number',
    'Stock (Count)', 
    'Cost', 
    'Selling Price', 
    'Discount', 
    'Commission (%)',
    'Type', 
    'Active Status'
]);

// Build Query
$filterClause = "";
if (!empty($filterCategory)) {
    $filterClause .= " AND p.category_id = '" . mysqli_real_escape_string($con, $filterCategory) . "'";
}
if (!empty($filterBrand)) {
    $filterClause .= " AND p.brand_id = '" . mysqli_real_escape_string($con, $filterBrand) . "'";
}
if (!empty($filterType)) {
    $filterClause .= " AND p.product_type = '" . mysqli_real_escape_string($con, $filterType) . "'";
}
if ($filterStatus !== '') {
    $filterClause .= " AND p.active_status = '" . mysqli_real_escape_string($con, $filterStatus) . "'";
}

// Query to fetch product details with individual batch information
$query = "
    SELECT 
        p.product_id,
        p.product_name,
        p.product_type,
        p.active_status,
        p.employee_commission_percentage,
        pb.batch_number,
        COALESCE(pb.quantity, 0) as batch_stock,
        COALESCE(pb.selling_price, 0) as selling_price,
        COALESCE(pb.cost, 0) as cost_price,
        pb.discount_price
    FROM products p
    LEFT JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
    WHERE 1=1 {$filterClause}
    ORDER BY p.product_name ASC, pb.created_at DESC
";

$result = mysqli_query($con, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Format values
        $isActive = $row['active_status'] == 1 ? 'Active' : 'Inactive';
        $discount = (isset($row['discount_price']) && $row['discount_price'] > 0) ? $row['discount_price'] : 0;
        $batchNumber = isset($row['batch_number']) ? $row['batch_number'] : '-';
        
        // Write to CSV
        fputcsv($output, [
            $row['product_id'],
            $row['product_name'],
            $batchNumber,
            $row['batch_stock'],
            $row['cost_price'],
            $row['selling_price'],
            $discount,
            $row['employee_commission_percentage'],
            ucfirst($row['product_type']),
            $isActive
        ]);
    }
}

fclose($output);
exit;
