<?php
require '../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $brand_name = mysqli_real_escape_string($con, $_POST['name']);

    $query = "INSERT INTO brands (brand_name) VALUES ('$brand_name')";
    $result = insert_query($query, "New Brand: " . $brand_name, "Add Brand");

    if ($result) {
        // Get the actual inserted ID directly from brands table
        $id_query = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' ORDER BY brand_id DESC LIMIT 1";
        $id_result = mysqli_query($con, $id_query);
        $row = mysqli_fetch_assoc($id_result);
        $last_id = $row['brand_id'];
        
        echo json_encode(['id' => $last_id, 'name' => $brand_name]);
    } else {
        echo json_encode(['error' => 'Error adding brand']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

mysqli_close($con);
