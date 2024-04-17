<?php
// $server = 'localhost';
// $db_user = 'root';
// $db_pwd = '';
// $db_name = 'srijaya';
$server = 'morgana.webserverlive.com';
$db_user = 'srijayal_system';
$db_pwd = 'system@123456';
$db_name = 'srijayal_system';
// Connect DB
$con = mysqli_connect($server, $db_user, $db_pwd, $db_name);

// Check DB connecting Erros
if (mysqli_connect_errno()) {
    die('Database connection failed' . mysqli_connect_error());
} else {
    //echo "Database Connection Successfull";
}
function end_db_con()
{
    global $con;
    mysqli_close($con);
}
?>
<?php
/// SQL Query Add Data to DB Function
function insert_query($query, $msg)
{
    if (!isset($msg)) {
        $msg = "to DB";
    }
    global $con;
    $result = mysqli_query($con, $query);
    if ($result) {
        echo "Recoard Added : {$msg} <br>";
    } else {
        $error = "Query Failed : Becouse, " . mysqli_error($con) . "<br>";
        echo $error;
        if (isset($error_array)) {
            array_push($error_array, $error);
        }
    }
}

// Function for Add Transaction Log
function transaction_log($transaction_type, $description, $amount)
{
    // Start Session if not started
    if (session_status() === PHP_SESSION_ACTIVE) {
        //echo "Session Active";
    } else {
        session_start();
    }

    if (isset($_SESSION['employee_id'])) {
        $employee_id = $_SESSION['employee_id'];
        $sql = "INSERT INTO transaction_log (transaction_type, description, amount, employ_id) VALUES ('$transaction_type', '$description', '$amount', '$employee_id');";
    } else {
        $sql = "INSERT INTO transaction_log (transaction_type, description, amount) VALUES ('$transaction_type', '$description', '$amount');";
    }
    insert_query($sql, "Transaction Log");
}
?>