<?php
require '../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $supplier_name = mysqli_real_escape_string($con, $_POST['name']);

    $query = "INSERT INTO suppliers (supplier_name) VALUES ('$supplier_name')";
    $result = insert_query($query, "New Supplier: " . $supplier_name, "Add Supplier");

    if ($result) {
        $last_id = mysqli_insert_id($con);
        echo json_encode(['id' => $last_id, 'name' => $supplier_name]);
    } else {
        echo json_encode(['error' => 'Error adding supplier']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
mysqli_close($con);
