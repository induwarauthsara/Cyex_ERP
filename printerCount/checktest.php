<?php
include 'conn.php';
// Check Real Balance and Database Balance are same or not

// Cost Sum from Database
$sql = "SELECT SUM(cost) as costSum FROM printer_counter_count";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $costSum = $row['costSum'];
}

// Deposit Sum from Database
$sql = "SELECT SUM(amount) as depositSum FROM printer_counter_bank_deposit";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $depositSum = $row['depositSum'];
}

// Balance from Database
$DatabaseBalance = $costSum - $depositSum;
// Show as Currency
$DatabaseBalance = number_format($DatabaseBalance, 2);

// Check Real Balance and Database Balance are same or not
if ($DatabaseBalance == $to_deposit_balance) {
    echo "<div style='color:#00663a'> <b>✅</b> Real Balance and Database <b> Balance are same.. :)  </b> </div>";
} else {
    echo "<div style='color:red'> <b>❌</b> Real Balance and Database <b> Balance are not same ! </b> </div>";
}

echo "<br>Sum of Printer Cost : " . $costSum;
echo "<br>Sum of Bank Deposit : " . $depositSum;
echo "<br>Database Balance : " . $DatabaseBalance;
echo "<br><br>To Deposit Balance : " . $to_deposit_balance;

?>