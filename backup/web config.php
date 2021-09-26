<?php
$server = 'thor.webserver.lk';
$db_user = 'srijayal_srijaya';
$db_pwd = 'srijaya@123';
$db_name = 'srijayal_srijaya';
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
        echo "Query Failed : Becouse, " . mysqli_error($con) . "<br>";
    }
}
?>