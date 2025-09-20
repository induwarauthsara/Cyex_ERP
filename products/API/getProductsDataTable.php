<?php
require_once '../../inc/config.php';

// Set header to JSON
header('Content-Type: application/json');

// Database connection parameters
$table = 'products';
$primaryKey = 'product_id';

// Define POST variables received from DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

// Get filter parameters
$filterCategory = isset($_POST['filterCategory']) ? $_POST['filterCategory'] : '';
$filterBrand = isset($_POST['filterBrand']) ? $_POST['filterBrand'] : '';
$filterType = isset($_POST['filterType']) ? $_POST['filterType'] : '';
$filterStatus = isset($_POST['filterStatus']) ? $_POST['filterStatus'] : '';

// Columns definition
$columns = [
    0 => 'p.product_id',
    1 => 'p.product_name',
    2 => 'p.barcode',
    3 => 'p.sku',
    4 => 'c.category_name',
    5 => 'b.brand_name',
    6 => 'p.product_type'
];

// Validate order direction
$orderDir = ($orderDir === 'asc') ? 'asc' : 'desc';

// Validate column index
if (!isset($columns[$orderColumn])) {
    $orderColumn = 0;
}

// SQL query count total records without filtering
$sqlTotal = "SELECT COUNT(*) as total FROM {$table}";
$totalResult = $con->query($sqlTotal);
$totalData = $totalResult->fetch_assoc()['total'];

// Initialize the response
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalData,
    "recordsFiltered" => 0,
    "data" => []
];

// Search clause
$searchClause = '';
if (!empty($search)) {
    $searchClause = " AND (
        p.product_name LIKE '%{$search}%' OR 
        p.barcode LIKE '%{$search}%' OR 
        p.sku LIKE '%{$search}%' OR 
        c.category_name LIKE '%{$search}%' OR 
        b.brand_name LIKE '%{$search}%' OR 
        p.product_type LIKE '%{$search}%'
    )";
}

// Filter clauses
$filterClause = '';
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

// SQL query count filtered records
$sqlFilterTotal = "
    SELECT COUNT(*) as total 
    FROM {$table} p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE 1=1 {$searchClause} {$filterClause}
";
$filterTotalResult = $con->query($sqlFilterTotal);
$filterTotal = $filterTotalResult->fetch_assoc()['total'];
$response["recordsFiltered"] = $filterTotal;

// Main query to get the data with filtering, ordering, and limiting
$sql = "
    SELECT 
        p.product_id,
        p.active_status,
        p.product_name,
        p.barcode,
        p.sku,
        p.product_type,
        p.stock_alert_limit,
        c.category_name,
        b.brand_name,
        COALESCE(pb.total_stock, 0) as total_stock,
        COALESCE(pb2.selling_price, 0) as latest_price,
        pb2.discount_price
    FROM {$table} p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN (
        SELECT 
            product_id, 
            SUM(quantity) as total_stock,
            MAX(created_at) as latest_date
        FROM product_batch 
        GROUP BY product_id
    ) pb ON p.product_id = pb.product_id
    LEFT JOIN product_batch pb2 ON pb.product_id = pb2.product_id AND pb.latest_date = pb2.created_at
    WHERE 1=1 {$searchClause} {$filterClause}
    ORDER BY {$columns[$orderColumn]} {$orderDir}
    LIMIT {$start}, {$length}
";

$result = $con->query($sql);

if ($result) {
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        $productName = htmlspecialchars($row['product_name'] ?? '');
        $barcode = htmlspecialchars($row['barcode'] ?? '');
        $sku = htmlspecialchars($row['sku'] ?? '');
        $categoryName = htmlspecialchars($row['category_name'] ?? '');
        $brandName = htmlspecialchars($row['brand_name'] ?? '');
        $productType = ucfirst($row['product_type'] ?? '');
        $totalStock = intval($row['total_stock'] ?? 0);
        $stockAlertLimit = intval($row['stock_alert_limit'] ?? 0);
        $latestPrice = floatval($row['latest_price'] ?? 0);
        $discountPrice = (isset($row['discount_price']) && $row['discount_price'] != null && $row['discount_price'] > 0) 
            ? floatval($row['discount_price']) 
            : null;
        $activeStatus = intval($row['active_status'] ?? 1);
        
        // Determine stock status
        $stockStatus = '';
        $stockClass = '';
        if ($totalStock <= 0) {
            $stockStatus = 'Out of Stock';
            $stockClass = 'stock-low';
        } elseif ($totalStock <= $stockAlertLimit) {
            $stockStatus = 'Low Stock';
            $stockClass = 'stock-medium';
        } else {
            $stockStatus = 'In Stock';
            $stockClass = 'stock-high';
        }
        
        // Format stock column
        $stockColumn = "<span class='stock-badge {$stockClass}'>{$stockStatus} ({$totalStock})</span>";
        
        // Format price column
        $priceColumn = "Rs. " . number_format($latestPrice, 2);
        
        // Format actions column
        $actionsColumn = "
            <button class='btn btn-action btn-view' onclick='viewProduct({$productId})'><i class='fas fa-eye'></i></button>
            <button class='btn btn-action btn-edit' onclick='editProduct({$productId})'><i class='fas fa-edit'></i></button>
            <button class='btn btn-action btn-toggle' onclick='toggleProductStatus({$productId}, {$activeStatus})' title='" . ($activeStatus ? 'Deactivate' : 'Activate') . "'><i class='fas fa-" . ($activeStatus ? 'toggle-on text-success' : 'toggle-off text-danger') . "'></i></button>
            <button class='btn btn-action btn-delete' onclick='deleteProduct({$productId}, \"{$productName}\")'><i class='fas fa-trash'></i></button>
        ";
        
        // Add status badge to product name
        $statusBadge = $activeStatus ? 
            "<span class='badge bg-success' style='margin-left:5px;'>Active</span>" : 
            "<span class='badge bg-danger' style='margin-left:5px;'>Inactive</span>";
        
        $productNameWithStatus = $productName . " " . $statusBadge;
        
        // Build the row
        $data[] = [
            'product_id' => $productId,
            'product_name' => $productNameWithStatus,
            'barcode' => $barcode,
            'sku' => $sku,
            'category_name' => $categoryName,
            'brand_name' => $brandName,
            'product_type' => $productType,
            'stock' => $stockColumn,
            'price' => $priceColumn,
            'discount_price' => $discountPrice,
            'actions' => $actionsColumn
        ];
    }
    
    $response["data"] = $data;
}

// Close connection
$con->close();

// Send response as JSON
echo json_encode($response); 