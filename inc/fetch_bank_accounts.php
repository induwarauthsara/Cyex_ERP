<?php
require_once '../inc/config.php';

$query = "SELECT `account_name` FROM `accounts` WHERE `account_type` = 'bank'";
$result = mysqli_query($con, $query);

$accounts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $accounts[] = $row['account_name'];
}

echo json_encode($accounts);

mysqli_close($con);
