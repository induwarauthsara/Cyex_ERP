<?php require_once '../../inc/config.php'; ?>
<?php require '../../inc/header.php';
include '../auth.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Shop Accounts</title>
</head>

<?php
function account_balance($account)
{
	global $con;
	$sql = "SELECT amount FROM accounts WHERE account_name ='$account'";
	$result = mysqli_query($con, $sql);
	$output = mysqli_fetch_assoc($result)['amount'];
	return $output;
}
?>

<body>
	<div id="account_balance">
		<h1>Shop Account Balance</h1>
		<b>Stock Account :</b> Rs. <?php echo account_balance("Stock Account") ?> <br>
		<b>Company Profit :</b> Rs. <?php echo account_balance("Company Profit") ?> <br>
		<b>Machines Account :</b> Rs. <?php echo account_balance("Machines Account") ?> <br>
		<b>Utility Bills :</b> Rs. <?php echo account_balance("Utility Bills") ?> <br>
	</div>
	<style>
		#account_balance {
			margin: 20px auto;
			width: 280px;
			line-height: 50px;
		}
	</style>
</body>

</html>

<?php end_db_con(); ?>