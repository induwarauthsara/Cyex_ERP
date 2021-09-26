<?php require_once 'config.php'; ?>
<?php
//print_r($_POST);
$customer = $_POST['cus'];
$sql = "SELECT customer_mobile FROM customers WHERE customer_name = '{$customer}'";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($recoard = mysqli_fetch_assoc($result)) {
        echo $recoard['customer_mobile'];
    }
    // print_r($recoard);
} else {
    echo "customer Not Found";
}
?>

<?php end_db_con(); ?>