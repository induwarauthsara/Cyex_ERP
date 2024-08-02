<?php
// 1. Update OneTimeProduct Table (UPDATE one_time_product SET status = 'skip' WHERE id = {$id})

require_once '../../inc/config.php';
$OneTimeProduct_id = $_GET['id'];

// 1. Update OneTimeProduct Table
$sql = "UPDATE oneTimeProducts_sales SET status = 'skip' WHERE oneTimeProduct_id = {$OneTimeProduct_id}";
$action = "Skip OneTimeProduct";
$msg = "Skip OneTimeProduct ID : {$id}";
insert_query($sql, $msg, $action);