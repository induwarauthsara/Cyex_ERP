<?php require_once '../inc/config.php';
require_once '../inc/header.php';
// Select Customer Name and ID
for ($i = 0; $i < 45; $i++) {
    $sql = "SELECT id, customer_name FROM customers WHERE id = '$i'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    if ($row) {
        $customer_name = $row['customer_name'];
        $customer_id = $row['id'];
        $customer_name_first_character_5 = substr($customer_name, 0, 10);


        // update invoice table customer id where customer name like customer name
        $sql = "UPDATE invoice SET customer_id = '$customer_id' , customer_name = '$customer_name' WHERE customer_name LIKE '$customer_name_first_character_5%';";
        echo $sql;

        // echo $customer_name;
        // echo "<br>";
        // echo $customer_id;
        // echo "<br>";
        echo "<br>";
    }
} 
// Select Customer ID