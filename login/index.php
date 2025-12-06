<?php session_start(); ?>
<?php require_once '../inc/config.php'; ?>

<?php
// Check is Logged in? Redirect to invoice
if (isset($_SESSION['employee_name'])) {
    header("Location: ../index.php");
}

$errors = array();

// SECURITY FIX: passwordDataLog function REMOVED
// This function was logging plaintext passwords to a file - MAJOR SECURITY VULNERABILITY
// Function permanently disabled for security reasons

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
                        <form id="loginForm" onsubmit="handleLogin(event)">
                            <div class="form-group">
                                <label class="form-control-label">USERNAME</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Username here" required>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">PASSWORD <i class="fa fa-eye-slash" aria-hidden="true" onclick="show_hide_pw()"></i></label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password here" required>
                            </div>

                            <div class="col-lg-12 loginbttm">
                                <div class="col-lg-6 login-btm login-text">
                                    <span id="errorMessage" style="color: #dc3545;"></span>
                                </div>
                                <div class="col-lg-6 login-btm login-button">
                                    <button type="submit" id="loginBtn" class="btn btn-outline-primary">LOGIN</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2"></div>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

            async function handleLogin(event) {
                event.preventDefault();
                
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                const errorMessage = document.getElementById('errorMessage');
                const loginBtn = document.getElementById('loginBtn');
                
                // Clear previous errors
                errorMessage.textContent = '';
                loginBtn.disabled = true;
                loginBtn.textContent = 'LOGGING IN...';
                
                try {
                    const response = await fetch('/api/v1/auth/login.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            username: username,
                            password: password
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.data && data.data.token) {
                        // Store JWT token
                        localStorage.setItem('auth_token', data.data.token);
                        
                        // Store user data
                        localStorage.setItem('employee_id', data.data.user.id);
                        localStorage.setItem('employee_name', data.data.user.name);
                        localStorage.setItem('employee_role', data.data.user.role);
                        
                        // Set session via PHP (for backward compatibility)
                        await fetch('/api/v1/auth/set_session.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + data.data.token
                            }
                        });
                        
                        // Redirect to main page
                        window.location.href = '../index.php';
                    } else {
                        errorMessage.textContent = data.message || 'Login failed';
                        loginBtn.disabled = false;
                        loginBtn.textContent = 'LOGIN';
                    }
                } catch (error) {
                    console.error('Login error:', error);
                    errorMessage.textContent = 'Connection error. Please try again.';
                    loginBtn.disabled = false;
                    loginBtn.textContent = 'LOGIN';
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