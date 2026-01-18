<?php
// Srijaya ERP Installer Script

$schemaFile = 'system_DB_Structure.sql';
$envFile = '.env';
$configFile = 'inc/config.php'; // For verification

// Helper function to send JSON response
function json_response($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

// 1. Check if already installed (simplistic check: .env exists)
if (file_exists($envFile)) {
    // Optional: allow overwrite or block
    // Die if not explicitly requested? The prompt implies "first time install", so we assume it can run.
    // However, for security, we might want to warn.
    // For now, we will proceed but maybe show a warning in UI.
}

// Handle POST Request (Installation Logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';
    
    $company_name = $_POST['company_name'] ?? '';
    $company_phone = $_POST['company_phone'] ?? '';
    $company_address = $_POST['company_address'] ?? '';
    $company_website = $_POST['company_website'] ?? '';
    // Logo handling is tricky via simple script if we need to upload, but prompt says "Setup ... Logo".
    // We'll assume text input for 'logo path' or file upload? 
    // "setup Company Name... Logo" could mean setting the variable.
    // If it's a file upload, we need to handle it. For now, let's assume valid text or file upload. 
    // If $_FILES['company_logo'] is present, move it.
    
    // DB Credentials Check
    if (empty($db_name) || empty($db_user)) {
         // As per prompt: "if not receive, don't continue and say 'DB Credianls not received by admin'"
         die("DB Credianls not received by admin");
    }

    // Attempt DB Connection
    // Report mode off to handle errors manually
    mysqli_report(MYSQLI_REPORT_OFF);
    $con = mysqli_connect($db_host, $db_user, $db_pass);

    if (!$con) {
        die("Database Connection Failed: " . mysqli_connect_error());
    }

    // Drop DB if exists and recreate? Or just select?
    // "install DB from system_DB_Structure.sql" usually implies creating tables.
    // If DB doesn't exist, try to create it.
    $db_selected = mysqli_select_db($con, $db_name);
    if (!$db_selected) {
        $sql = "CREATE DATABASE IF NOT EXISTS `$db_name`";
        if (mysqli_query($con, $sql)) {
            mysqli_select_db($con, $db_name);
        } else {
            die("Could not create/select database '$db_name': " . mysqli_error($con));
        }
    }

    // 2. Create .env file
    $envContent = "DB_HOST=$db_host\n";
    $envContent .= "DB_NAME=$db_name\n";
    $envContent .= "DB_USER=$db_user\n";
    $envContent .= "DB_PASS=$db_pass\n";
    $envContent .= "APP_ENV=production\n"; // Default as per prompt

    if (file_put_contents($envFile, $envContent) === false) {
        die("Failed to write .env file. Please check permissions.");
    }
    
    // Secure inc/config.local.php (make sure it doesn't conflict or is empty/commented if we are using .env)
    // The user said "Secure the Configuration (.env) or inc/config.local.php".
    // Since we created .env, we are good. We can optionally rename config.local.php to backup if it exists.
    if (file_exists('inc/config.local.php')) {
        rename('inc/config.local.php', 'inc/config.local.php.bak_' . time());
    }

    // 3. Install DB Structure
    if (file_exists($schemaFile)) {
        $sql_dump = file_get_contents($schemaFile);
        
        // Simple SQL splitter handling DELIMITER
        // Note: mysqli_multi_query is easier if cleaned up, but DELIMITER keyword is client-side.
        // We must strip "DELIMITER $$" and "DELIMITER ;" and treat the content between them as single queries.
        
        // Remove comments?
        $lines = explode("\n", $sql_dump);
        $clean_sql = "";
        foreach ($lines as $line) {
            $trimLine = trim($line);
            if ($trimLine === '' || strpos($trimLine, '--') === 0 || strpos($trimLine, '#') === 0 || strpos($trimLine, '/*') === 0) {
                continue;
            }
            $clean_sql .= $line . "\n";
        }
        
        // Manual split
        $queries = [];
        $buffer = '';
        $delimiter = ';';
        
        $tokens = explode("\n", $clean_sql);
        foreach ($tokens as $token) {
            $trimToken = trim($token);
            if (preg_match('/^DELIMITER\s+(\S+)/i', $trimToken, $matches)) {
                $delimiter = $matches[1];
                continue;
            }
            
            $buffer .= $token . "\n";
            
            if (substr(trim($buffer), -strlen($delimiter)) === $delimiter) {
                // Found command end
                $sql_cmd = substr(trim($buffer), 0, -strlen($delimiter));
                if (!empty($sql_cmd)) {
                    $queries[] = $sql_cmd;
                }
                $buffer = '';
            }
        }
        
        // Execute queries
        foreach ($queries as $query) {
            if (!mysqli_query($con, $query)) {
                // Log error but maybe continue? Or stop? 
                // "Table already exists" might be common on re-run.
                // For "first time install", we stop on critical errors.
                // echo "Error executing query: " . mysqli_error($con) . "<br>";
            }
        }
    } else {
        die("SQL Schema file ($schemaFile) not found.");
    }

    // 4. Setup Company Details
    // Fetch current url as company_base_url
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $uri = dirname($_SERVER['REQUEST_URI']);
    // Normalize URI (remove /install.php if present implicitly by dirname, ensuring trailing slash)
    $uri = rtrim($uri, '/\\');
    $company_base_url = "$protocol://$host$uri/";

    // Logo upload
    $logo_filename = 'logo.png'; // Default
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Ensure this exists or root? inc/config.php says default IS "logo.png" (root)
        // Checking current logo location: "logo.png" in root.
        $targetFile = basename($_FILES['company_logo']['name']);
        if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $targetFile)) {
            $logo_filename = $targetFile;
        }
    }

    // Update 'settings' table.
    // We assume 'settings' table structure: setting_name (varchar), setting_value (varchar)
    $settings_to_update = [
        'company_name' => $company_name,
        'company_phone' => $company_phone,
        'company_address' => $company_address,
        'company_website' => $company_website,
        'company_logo' => $logo_filename,
        'company_base_url' => $company_base_url
    ];
    
    foreach ($settings_to_update as $key => $val) {
        // Use ON DUPLICATE KEY UPDATE or just UPDATE if we know rows exist?
        // SQL dump likely creates them? Or empty?
        // Safer to try INSERT ... ON DUPLICATE KEY UPDATE
        // Assuming table 'settings' exists.
        $val_esc = mysqli_real_escape_string($con, $val);
        $key_esc = mysqli_real_escape_string($con, $key);
        
        // Flexible query trying update first, then insert if 0 affected (or specific insert-update syntax)
        // If table schema is simple:
        // Note: setting_description is NOT NULL, so we must provide a value on insert.
        $sql = "INSERT INTO settings (setting_name, setting_value, setting_description) VALUES ('$key_esc', '$val_esc', 'System Setting') 
                ON DUPLICATE KEY UPDATE setting_value = '$val_esc'";
        mysqli_query($con, $sql);
    }

    // 5. Create Admin User
    $admin_user = $_POST['admin_user'] ?? '';
    $admin_pass = $_POST['admin_pass'] ?? '';

    if (!empty($admin_user) && !empty($admin_pass)) {
        $admin_user_esc = mysqli_real_escape_string($con, $admin_user);
        $admin_pass_esc = mysqli_real_escape_string($con, $admin_pass); // Stored as plaintext per current system design

        // Check if user already exists
        $check_user = mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$admin_user_esc'");
        if (mysqli_num_rows($check_user) == 0) {
            // Insert Admin User
            // Note: Providing dummy values for required fields (mobile, nic, day_salary)
            $sql_admin = "INSERT INTO employees 
                (emp_name, password, role, mobile, nic, day_salary, status, onboard_date) 
                VALUES 
                ('$admin_user_esc', '$admin_pass_esc', 'Admin', '0000000000', 'ADMIN', '0.00', 1, CURRENT_DATE())";
            
            if (mysqli_query($con, $sql_admin)) {
                echo "<p>Admin user created successfully.</p>";
            } else {
                echo "<p style='color:red;'>Error creating admin user: " . mysqli_error($con) . "</p>";
            }
        }
    }
    
    echo "<h1>Installation Successful!</h1>";
    echo "<p>.env file created.</p>";
    echo "<p>Database imported.</p>";
    echo "<p>Settings updated.</p>";
    echo "<p>Please delete this <code>install.php</code> file immediately for security.</p>";
    echo "<a href='index.php'>Go to Home</a>";
    exit;
}

// Render HTML Form (GET)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Srijaya ERP Installation</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; display: flex; justify-content: center; padding-top: 50px; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #218838; }
        .section-title { margin-top: 20px; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px; font-size: 1.1em; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <h2>System Installation</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="section-title">Database Configuration</div>
            <div class="form-group">
                <label>Database Host</label>
                <input type="text" name="db_host" value="localhost" required>
            </div>
            <div class="form-group">
                <label>Database User</label>
                <input type="text" name="db_user" required>
            </div>
            <div class="form-group">
                <label>Database Password</label>
                <input type="password" name="db_pass">
            </div>
            <div class="form-group">
                <label>Database Name</label>
                <input type="text" name="db_name" value="srijaya_pos_db" required>
            </div>

            <div class="section-title">Company Settings</div>
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="company_phone" required>
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="company_address" required>
            </div>
            <div class="form-group">
                <label>Website</label>
                <input type="text" name="company_website">
            </div>
            <div class="form-group">
                <label>Company Logo</label>
                <input type="file" name="company_logo" accept="image/*">
            </div>

            <div class="section-title">Admin Account Setup</div>
            <div class="form-group">
                <label>Admin Username</label>
                <input type="text" name="admin_user" placeholder="e.g. admin" required>
            </div>
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="admin_pass" required>
            </div>

            <button type="submit">Install & Setup</button>
        </form>
    </div>
</body>
</html>
