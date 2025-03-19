<?php
require_once '../../inc/config.php';

$product_id = $_GET['product_id'];

$query = "SELECT batch_id, selling_price, discount_price, cost, profit, status, quantity, restocked_at, batch_number AS batch_name FROM product_batch WHERE product_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

$batches = [];
while ($row = $result->fetch_assoc()) {
    $batches[] = $row;
}

echo json_encode($batches);

$stmt->close();
$con->close();
