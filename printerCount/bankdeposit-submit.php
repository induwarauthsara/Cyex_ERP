<?php
include 'conn.php';
// Check Post Request available
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the amount field is empty
    if (empty($_POST['amount'])) {
        echo "Amount is required";
    } else {
        // Get the amount from the form
        $amount = $_POST['amount'];

        // Insert the amount into the bank_deposit table
        $sql = "INSERT INTO printer_counter_bank_deposit (amount) VALUES ('$amount')";
        $result = $con->query($sql);

        // Check if the query execution was successful
        if ($result === false) {
            echo "Error: " . $sql . "<br>" . $con->error;
        } else {
            echo "Amount Insert successfully <br>";
        }

        // Update the statistics table
        $sql = "UPDATE printer_counter_statistics SET amount = amount - $amount WHERE name = 'to_deposit_balance'";
        $result = $con->query($sql);

        // Check if the query execution was successful
        if ($result === false) {
            echo "Error: " . $sql . "<br>" . $con->error;
        } else {
            echo "Statistics Update successfully <br>";
        }
    }
    // Close the database connection
    $con->close();

    // Redirect to the bankDeposit.php page
    header("Location: bankdeposit.php");
}
