<?php
// Establish database connection
require_once '../inc/config.php';

$item_name = $_GET['item_name'];

$query = "SELECT id, cost FROM items WHERE item_name = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $item_name);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id, $price);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Prepare response JSON
$response = array("id" => $id, "price" => $price);

echo json_encode($response);

mysqli_close($con);
?>
