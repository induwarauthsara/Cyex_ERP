<?php
require '../../inc/config.php';

$query = "SELECT category_id AS id, category_name AS name FROM categories";
$categories = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($categories);

mysqli_close($con);
