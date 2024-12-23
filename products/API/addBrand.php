<?php
require '../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $brand_name = mysqli_real_escape_string($con, $_POST['name']);

    $query = "INSERT INTO brands (brand_name) VALUES ('$brand_name')";
    $result = insert_query($query, "New Brand: " . $brand_name, "Add Brand");

    if ($result) {
        $last_id = mysqli_insert_id($con);
        echo json_encode(['id' => $last_id, 'name' => $brand_name]);
    } else {
        echo json_encode(['error' => 'Error adding brand']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

mysqli_close($con);
