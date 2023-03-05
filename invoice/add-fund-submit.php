<?php
require_once '../inc/config.php';

if (isset($_GET['amount']) && isset($_GET['bill'])) {

    echo "<pre>";
    print_r($_GET);
    echo "</pre>";

    // From Get Request Parameters
    $amount = $_GET['amount'];
    $invoice_number = $_GET['bill'];

    // From Database
    $sql = "SELECT * FROM invoice WHERE invoice_number = $invoice_number";
    $result = mysqli_query($con, $sql);
    $invoice_details = mysqli_fetch_assoc($result);

    // Invoice details
    $total =  $invoice_details['total'];
    echo "total : " . $total . "<br>";
    $advance =  $invoice_details['advance'];
    echo "advance : " . $advance . "<br>";
    $balance =  $invoice_details['balance'];
    echo "balance : " . $balance . "<br>";

    $new_advance_amount = $advance + $amount;
    echo "new_advance_amount : " . $new_advance_amount . "<br>";
    $new_balance_amount = $total - $new_advance_amount;
    echo "new_balance_amount : " . $new_balance_amount . "<br>";

    // check full paid
    if ($new_balance_amount <= 0.00) {
        $full_paid = 1;
    } else {
        $full_paid = 0;
    }

    // Add Fund to Invoice
    $sql = "UPDATE `invoice` SET `advance` = $new_advance_amount, `balance` = $new_balance_amount, `full_paid` = $full_paid WHERE `invoice`.`invoice_number` = $invoice_number;";
    // echo $sql . "<br>";
    insert_query($sql, "Insert Petty Cash to Tabel");

    // Add Fund to "cash in hand'
    $sql = "UPDATE accounts SET amount = amount + {$amount} WHERE account_name = 'cash_in_hand'";
    insert_query($sql, "Add Fund to 'Cash in Hand' Account");

    end_db_con();
}
echo '<script>
        // Redirect Page
        setTimeout(location.href = "index.php", 100);
        </script>';
die();
