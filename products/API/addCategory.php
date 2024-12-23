<?php
require '../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $category_name = mysqli_real_escape_string($con, $_POST['name']);

    $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    $result = insert_query($query, "New Category: " . $category_name, "Add Category");

    if ($result) {
        $last_id = mysqli_insert_id($con);
        echo json_encode(['id' => $last_id, 'name' => $category_name]);
    } else {
        echo json_encode(['error' => 'Error adding category']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

mysqli_close($con);
