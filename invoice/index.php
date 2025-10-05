<?php require_once '../inc/config.php';
require_once '../inc/header.php';
include  __DIR__ . '/../inc/DataTable_cdn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>

<body>
    <center>
        <h1>All Invoices</h1>
    </center>

    <?php
    $sql = "SELECT * 
            FROM invoice i
            INNER JOIN employees e ON i.biller = e.employ_id
            ORDER BY invoice_number DESC
            LIMIT 1000;"; // Add 1000 limit to avoid overload of data
    include 'DataTable of DB Invoice Table.php'; ?>

</body>

</html>



<?php end_db_con(); ?>