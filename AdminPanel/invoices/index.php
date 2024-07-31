<?php
include '../nav.php';
require_once $_SERVER['SERVER_NAME'] . './inc/config.php';
require_once $_SERVER['SERVER_NAME'] . '/inc/header.php';
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
    $sql = "SELECT * FROM invoice ORDER BY invoice_number DESC;";
    include '../../invoice/DataTable of DB Invoice Table.php'; ?>

</body>

</html>



<?php end_db_con(); ?>