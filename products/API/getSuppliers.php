<?php
require '../../inc/config.php';

$query = "SELECT supplier_id AS id, supplier_name AS name FROM suppliers";
$suppliers = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($suppliers);

mysqli_close($con);
