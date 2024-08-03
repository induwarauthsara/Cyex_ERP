<?php
include 'nav.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['error_id'])) {
    $error_id = $_POST['error_id'];
    $sql = "UPDATE `error_log` SET `status` = 'fixed' WHERE `error_id` = $error_id;";
    insert_query($sql, "Error ID : $error_id", "Error Solved");
    echo "<script>location.href='erros.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Erros</title>

    <style>
        #cards {
            flex-direction: column;
            justify-content: center;
            /* width: 100%; */
        }

        .card {
            flex-direction: column;
            justify-content: left;
            width: 100% !important;
            gap: 5px;
        }

        button {
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: large;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php $sql = "SELECT COUNT(*) FROM `error_log` WHERE `status` = 'pending';";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $error_count = mysqli_fetch_array($result);
        $error_count = $error_count['COUNT(*)'];
        if ($error_count > 0) {
            echo "<h1>$error_count System Erros Found </h1>";
        }
    } else {
        echo "Database Query Failed";
    } ?>
    <div id="cards">
        <?php
        $sql = "SELECT error_log.*, emp_name FROM `error_log`
                INNER JOIN employees
                WHERE employee_id = employ_id AND error_log.`status` = 'pending';";
        $result = mysqli_query($con, $sql);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                while ($error = mysqli_fetch_array($result)) {
                    echo "<div class='card'>";
                    echo "<h3>Error ID: " . $error['error_id'] . "</h3>";
                    echo "<p><b>Error</b> <br> Code " . $error['error_code'] . "</p><br>";
                    echo "<p><b>Error</b> <br> Message " . $error['error_message'] . "</p><br>";
                    echo "<p><b>Query</b> <br> " . $error['query'] . "</p><br>";
                    echo "<p><b>Action</b> <br> " . $error['action'] . "</p><br>";
                    echo "<p><b>Description</b> <br> " . $error['action_description'] . "</p><br>";
                    echo "<p><b>Date</b> <br> " . $error['date'] . "</p><br>";
                    echo "<p><b>Time</b> <br> " . $error['time'] . "</p><br>";
                    echo "<p><b>Employee</b> <br> " . $error['emp_name'] . "</p><br>";
                    echo "<p><b>Status</b> <br> " . $error['status'] . "</p><br>";
                    // Add a button to solve the error
                    echo "<form action='' method='post'>";
                    echo "<button type='input' name='error_id' value='" . $error['error_id'] . "'> Solve </button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<h1>No any System error found</h1>";
            }
        } else {
            echo "Database Query Failed";
        }
        ?>
    </div>
</body>

</html>