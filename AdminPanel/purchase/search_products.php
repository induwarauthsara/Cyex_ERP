<?php
require_once('../../../inc/config.php');

header('Content-Type: application/json');

if (!isset($_GET['search'])) {
    echo json_encode([]);
    exit;
}

$search = mysqli_real_escape_string($con, $_GET['search']);

try {
    $query = "SELECT p.*, pb.cost
              FROM products p
              LEFT JOIN (
                  SELECT product_id, cost
                  FROM product_batch
                  WHERE batch_id IN (
                      SELECT MAX(batch_id)
                      FROM product_batch
                      GROUP BY product_id
                  )
              ) pb ON p.product_id = pb.product_id
              WHERE p.product_name LIKE '%$search%'
                 OR p.barcode LIKE '%$search%'
                 OR p.sku LIKE '%$search%'
              ORDER BY p.product_name
              LIMIT 50";

    $result = mysqli_query($con, $query);

    if (!$result) {
        throw new Exception(mysqli_error($con));
    }

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['cost'] = $row['cost'] ?? 0;
        $products[] = $row;
    }

    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
