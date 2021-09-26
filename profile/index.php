<?php require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>
<?php $employee_name = $_SESSION['employee_name'];
$employee_id = $_SESSION['employee_id']; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>

<body>
    <h1>
        <center>
            Hello <?php echo $employee_name ?> , <br>
            This Feature is still Under Connection..
        </center>
    </h1>
</body>

</html>

<?php end_db_con(); ?>