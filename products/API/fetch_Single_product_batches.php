<?php
require_once '../../inc/config.php';

header('Content-Type: application/json');

if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

$product_id = (int)$_GET['product_id'];

$query = "SELECT 
    batch_id, 
    batch_number AS batch_name, 
    cost, 
    selling_price, 
    profit,
    quantity, 
    alert_quantity,
    supplier_id,
    status, 
    expiry_date,
    discount_price,
    restocked_at,
    created_at,
    notes
FROM product_batch 
WHERE product_id = ? 
ORDER BY 
    CASE WHEN status = 'active' THEN 1 ELSE 2 END,
    quantity DESC, 
    restocked_at DESC";

$stmt = $con->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

$batches = [];
while ($row = $result->fetch_assoc()) {
    // Format the data for frontend
    $row['quantity'] = number_format($row['quantity'], 3);
    $row['cost'] = number_format($row['cost'], 2);
    $row['selling_price'] = number_format($row['selling_price'], 2);
    $row['profit'] = number_format($row['profit'], 2);
    $row['alert_quantity'] = number_format($row['alert_quantity'], 3);
    $row['discount_price'] = $row['discount_price'] ? number_format($row['discount_price'], 2) : null;
    $row['expiry_date'] = $row['expiry_date'] ?: null;
    $row['display_name'] = $row['batch_name'] . ' (Qty: ' . $row['quantity'] . ', Status: ' . ucfirst($row['status']) . ')';
    
    $batches[] = $row;
}

echo json_encode($batches);

$stmt->close();
$con->close();
?>
