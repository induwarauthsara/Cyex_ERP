<?php
require '../../inc/config.php';

$query = "SELECT brand_id AS id, brand_name AS name FROM brands";
$brands = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($brands);

mysqli_close($con);
