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
    <title>Pending Payments</title>
</head>

<?php
$sql = "SELECT sum(balance) from invoice where balance >= 0;";
$payment_pending_total = asRs(mysqli_fetch_assoc(mysqli_query($con, $sql))['sum(balance)']); ?>

<body>
    <center>
        <h1>Payment Pending Invoices (Total : <?php echo $payment_pending_total; ?> )</h1>
    </center>

    <?php
    $sql = "SELECT * FROM invoice WHERE balance > 0;";
    include 'DataTable of DB Invoice Table.php'; ?>

</body>

</html>



<?php end_db_con(); ?>