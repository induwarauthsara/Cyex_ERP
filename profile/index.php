<?php require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>
<?php $employee_name = $_SESSION['employee_name'];
$employee_id = $_SESSION['employee_id'];
$employee_role = $_SESSION['employee_role']; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $employee_name ?> | Profile </title>
    <link rel="stylesheet" href="profile.css">
</head>

<body>
    <div class="content-wrapper">
        <div id="user_details">
            <div class="name">
                <h1>
                    <?php echo $employee_name ?> <br>
                </h1>
                <h4>
                    <?php echo $employee_role ?>
                </h4>
            </div>
            <i class="fas fa-user fa-5x"></i>
        </div>
        <br><br>
        <div>
            <div>
                <?php $sql = "SELECT * FROM employees WHERE emp_name = '$employee_name' AND employ_id = '$employee_id' AND `status` = '1'";
                $result = mysqli_query($con, $sql);
                if ($result) {
                    // qury success
                    if (mysqli_num_rows($result) == 1) {
                        $employe = mysqli_fetch_assoc($result);
                    } else {
                        echo "No Result Found";
                    }
                } else {
                    echo "Database Query Failed";
                }
                ?>
            </div>
            <i class="fas fa-hand-holding-usd fa-2x"></i> RS. <?php echo $employe['salary']; ?>

        </div>


    </div>
</body>

</html>

<?php end_db_con(); ?>