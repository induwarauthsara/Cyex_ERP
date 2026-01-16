<?php
// Load Configuration
$con = false; // Initialize to false by default to prevent undefined variable errors
$envPath = __DIR__ . '/../.env';
$localConfigPath = __DIR__ . '/config.local.php';

if (file_exists($envPath)) {
    // Parse .env file manually to avoid dependency on vlucas/phpdotenv for simple install
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
    
    $server = $_ENV['DB_HOST'] ?? 'localhost';
    $db_user = $_ENV['DB_USER'] ?? 'root';
    $db_pwd = $_ENV['DB_PASS'] ?? '';
    $db_name = $_ENV['DB_NAME'] ?? '';
} elseif (file_exists($localConfigPath)) {
    require_once $localConfigPath;
    // Map $db_server (from example) to $server (used in connection) if needed
    if (isset($db_server) && !isset($server)) {
        $server = $db_server;
    }
} else {
    // Fallback or Error
    // Check if we are in the install process
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requestUri, 'install.php') === false) {
         die("Configuration Error: .env or inc/config.local.php not found. Please run <a href='install.php'>install.php</a>.");
    }
}

// Connect DB
if (isset($server) && isset($db_user) && isset($db_pwd) && isset($db_name)) {
    try {
        $con = mysqli_connect($server, $db_user, $db_pwd, $db_name);
    } catch (Exception $e) {
        $con = false;
    }
}

// Set Timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Check DB connecting Erros
if ($con && mysqli_connect_errno()) {
    die('Database connection failed' . mysqli_connect_error());
} else {
    //echo "Database Connection Successfull";
}

function end_db_con()
{
    global $con;
    if ($con) mysqli_close($con);
}

// Initialize Default Company Details
$ERP_COMPANY_NAME = "POS"; // Default
$ERP_COMPANY_ADDRESS = "(Address)"; // Default
$ERP_COMPANY_PHONE = ""; // Default
$ERP_COMPANY_LOGO = "logo.png"; // Default
$ERP_COMPANY_BASE_URL = ""; // Default
$ERP_COMPANY_WEBSITE = ""; // Default

// Fetch Company Details from Database
if ($con) {
    try {
        $settingQuery = "SELECT setting_name, setting_value FROM settings WHERE setting_name IN ('company_name', 'company_address', 'company_phone', 'company_logo', 'company_base_url', 'company_website')";
        $settingResult = mysqli_query($con, $settingQuery);
        if ($settingResult) {
            while ($row = mysqli_fetch_assoc($settingResult)) {
                switch ($row['setting_name']) {
                    case 'company_name':
                        $ERP_COMPANY_NAME = $row['setting_value'];
                        break;
                    case 'company_address':
                        $ERP_COMPANY_ADDRESS = $row['setting_value'];
                        break;
                    case 'company_phone':
                        $ERP_COMPANY_PHONE = $row['setting_value'];
                        break;
                    case 'company_logo':
                        $ERP_COMPANY_LOGO = $row['setting_value'];
                        break;
                    case 'company_base_url':
                        $ERP_COMPANY_BASE_URL = $row['setting_value'];
                        break;
                    case 'company_website':
                        $ERP_COMPANY_WEBSITE = $row['setting_value'];
                        break;
                }
            }
        }
    } catch (Exception $e) {
        // Silently fail to defaults if table doesn't exist or error occurs
    }
}

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
    
    if (!$con) {
        return false;
    }

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
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ississ', $error_code, $error_message, $query, $employee_id, $action, $msg);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if (isset($error_array)) {
            array_push($error_array, $error_message);
        }
    }
        return $result;
}

function fetch_data($query) {
    global $con;
    if (!$con) return [];
    
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
    
    // Check if employee_id is set in session
    $employee_id = $_SESSION['employee_id'] ?? 0;

    if (isset($_SESSION['employee_id'])) {
        $sql = "INSERT INTO transaction_log (transaction_type, description, amount, employ_id) VALUES ('$transaction_type', '$description', '$amount', '$employee_id');";
    } else {
        $sql = "INSERT INTO transaction_log (transaction_type, description, amount) VALUES ('$transaction_type', '$description', '$amount');";
    }
    insert_query($sql, "Transaction Type : $transaction_type, description : $description, Rs. $amount by $employee_id ", "Transaction Log");
}
?>