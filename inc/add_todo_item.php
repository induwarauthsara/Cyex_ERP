<?php require_once 'config.php';
session_start(); ?>

<?php print_r($_GET);

$todoName = $_GET['todoName'];
$todoTime = $_GET['todoTime'];
$sql = "INSERT INTO `todo`(`title`, `submision_time`) VALUES ('$todoName','$todoTime')";
insert_query($sql, "Add Todo Item Data to Database");

?>

<?php end_db_con(); ?>