<?php require_once 'config.php';
session_start(); ?>

<?php print_r($_GET);

$todoName = $_GET['todoName'];
$todoTime = $_GET['todoTime'];
$sql = "INSERT INTO `todo`(`title`, `submision_time`) VALUES ('$todoName','$todoTime')";
insert_query($sql, "Name : $todoName, Time : $todoTime", "Add Todo Item");

?>

<?php end_db_con(); ?>