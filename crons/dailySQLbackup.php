<?php
require_once '../inc/config.php';

// 2. Send Database Backup to Email
// Email details
$to = "induwarauthsara19@gmail.com";
$subject = date('Y-m-d') . " $ERP_COMPANY_NAME System Database Backup";
$message = "Please find attached the database backup for " . date('Y-m-d');
$headers = "From: DBbackup@cyextech.com"; // Replace with your email address

// Command to execute mysqldump and capture output
$command = "mysqldump --opt -h $server -u $db_user -p$db_pwd $db_name";
$output = shell_exec($command);
echo "<pre>$output</pre>";

// Check if the command was successful
if ($output !== null) {
    // Define boundary for separating the different parts of the email
    $uid = md5(uniqid(time()));

    // Construct the email headers
    $headers = "From: DBbackup@cyextech.com\r\n";
    $headers .= "Reply-To: induwarauthsara19@gmail.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$uid\"\r\n\r\n";

    // Start the body of the email
    $body = "This is a multi-part message in MIME format.\r\n";
    $body .= "--$uid\r\n";
    $body .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $message . "\r\n\r\n";
    $body .= "--$uid\r\n";
    $body .= "Content-Type: application/octet-stream; name=\"" . date('Y-m-d') . " {$ERP_COMPANY_NAME} backup.sql\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . date('Y-m-d') . " {$ERP_COMPANY_NAME} backup.sql\"\r\n\r\n";
    $body .= chunk_split(base64_encode($output)) . "\r\n\r\n";
    $body .= "--$uid--";

    // Send the email
    if (mail($to, $subject, $body, $headers)) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email.";
    }
} else {
    echo "Database backup failed.";
}
