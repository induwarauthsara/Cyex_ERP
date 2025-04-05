<?php
require '../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $category_name = mysqli_real_escape_string($con, $_POST['name']);

    $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    $result = insert_query($query, "New Category: " . $category_name, "Add Category");

    if ($result) {
        // Get the actual inserted ID directly from categories table
        $id_query = "SELECT category_id FROM categories WHERE category_name = '$category_name' ORDER BY category_id DESC LIMIT 1";
        $id_result = mysqli_query($con, $id_query);
        $row = mysqli_fetch_assoc($id_result);
        $last_id = $row['category_id'];
        
        echo json_encode(['id' => $last_id, 'name' => $category_name]);
    } else {
        echo json_encode(['error' => 'Error adding category']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

mysqli_close($con);
