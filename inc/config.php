<?php
// $server = 'localhost';
// $db_user = 'root';
// $db_pwd = '';
// $db_name = 'srijaya';
$server = 'athena.webserverlive.com';
$db_user = 'srijayapos_u1';
$db_pwd = 'srijaya@P2';
$db_name = 'srijayapos_new';

// $server = 'morgana.webserverlive.com';
// $db_user = 'srijayal_ifix';
// $db_pwd = 'ifix_DP_PW@123';
// $db_name = 'srijayal_ifix';
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

$ERP_COMPANY_NAME = "Global Mart";
$ERP_COMPANY_ADDRESS = "(Address Here)";
$ERP_COMPANY_PHONE = "(Phone Number Here)";
// Start Session if not started
if (session_status() === PHP_SESSION_ACTIVE) {
    //echo "Session Active";
} else {
    session_start();
}

if (!isset($employee_id) && isset($_SESSION['employee_id']) ){
    $employee_id = $_SESSION['employee_id'];
}else{
    $employee_id = 0;
}

/// SQL Query Add Data to DB Function
function insert_query($query, $msg, $action)
{
    // Disable Exception Mode for MySQLi
    mysqli_report(MYSQLI_REPORT_OFF);

    // Start Session if not started
    if (session_status() === PHP_SESSION_ACTIVE) {
        //echo "Session Active";
    } else {
        session_start();
    }

    if (!isset($msg)) {
        $msg = "to DB";
    }

    if (!isset($action)) {
        $action = "-";
    }

    global $con;
    global $result;
    $result = mysqli_query($con, $query);
    if ($result) {
        // echo "Record Added : {$msg} <br>";
        // Save in Action Log Table
        if (isset($_SESSION['employee_id'])) {
            $employee_id = $_SESSION['employee_id'];
            // CREATE TABLE action_log ( id INT AUTO_INCREMENT PRIMARY KEY, employee_id  INT NOT NULL, action VARCHAR(20) NOT NULL, description TEXT, date DATE DEFAULT CURRENT_DATE, time TIME DEFAULT CURRENT_TIME );
            $sql = "INSERT INTO action_log (employee_id, action, description) VALUES ('$employee_id', '$action', '$msg');";
        } else {
            $sql = "INSERT INTO action_log (action, description) VALUES ('$action', '$msg');";
        }
        mysqli_query($con, $sql);
    } else {
        // Save error in Error Log Table
        
        if (isset($_SESSION['employee_id'])) {
            $employee_id = $_SESSION['employee_id'];
        }else{
            $employee_id = 0;
        }

        // Capture the error details
        $error_code = mysqli_errno($con);
        $error_message = mysqli_error($con);

        // Insert the error details into the error_log table
        $logQuery = "INSERT INTO error_log (error_code, error_message, query, date, time, employee_id, action, action_description) VALUES (?, ?, ?, CURRENT_DATE, CURRENT_TIME, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $logQuery);
        mysqli_stmt_bind_param($stmt, 'ississ', $error_code, $error_message, $query, $employee_id, $action, $msg);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);


        if (isset($error_array)) {
            array_push($error_array, $error_message);
        }
    }
        return $result;

    // Enable Exception Mode for MySQLi
    // mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

function fetch_data($query) {
    global $con;
    $result = mysqli_query($con, $query);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_free_result($result);
    }
    return $data;
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
    insert_query($sql, "Transaction Type : $transaction_type, description : $description, Rs. $amount by $employee_id ", "Transaction Log");
}
?>