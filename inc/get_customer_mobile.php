<?php require_once 'config.php'; 
//print_r($_POST);
$customer = $_POST['cus'];
$sql = "SELECT customer_mobile FROM customers WHERE customer_name = '{$customer}'";

$result = mysqli_query($con, $sql);

if ($result) {
    if (mysqli_num_rows($result) == 0) {
        echo "customer Not Found";
    } else {
        while ($recoard = mysqli_fetch_assoc($result)) {
            echo $recoard['customer_mobile'];
        }
    }
    // print_r($recoard);
} else {
    echo "Error : " . mysqli_error($con);
}
end_db_con(); ?>