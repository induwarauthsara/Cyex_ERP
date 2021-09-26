<?php require_once 'config.php'; ?>
<?php
//print_r($_POST);
$product = $_POST['product'];
$sql = "SELECT rate FROM products WHERE product_name = '{$product}'";
$result = mysqli_query($con, $sql);
if ($result) {
    while ($recoard = mysqli_fetch_assoc($result)) {
        echo $recoard['rate'];
    }
    // print_r($recoard);
} else {
    echo "Product Not Found";
}
?>
<?php end_db_con(); ?>