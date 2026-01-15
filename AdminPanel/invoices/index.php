<?php
include '../nav.php';
// require_once(__DIR__ . '../../../inc/header.php');
// require_once(__DIR__ . '../../../inc/config.php');
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
            WHERE i.is_deleted = 0
            ORDER BY invoice_number DESC;";
    include '../../invoice/DataTable of DB Invoice Table.php'; ?>

</body>

</html>



<?php end_db_con(); ?>