<?php
require_once '../inc/config.php';

// 1. Send Money to Lochana Wallet (Cancelled)
// 2. Send Database Backup to Email

// 1. Send Money to Lochana Wallet
// Write the SQL query to insert the data
// $amount = 3000;
// $description = 'Day Salary';
// $sql = "INSERT INTO lochana_wallet (amount, description) VALUES ('$amount', '$description');";
// if ($con->query($sql) === TRUE) {
//     echo "New record created successfully";
// } else {
//     echo "Error: " . $sql . "<br>" . $con->error;
// }
// $sql = "UPDATE accounts SET amount = amount - $amount WHERE account_name = 'cash_in_hand'";
// if ($con->query($sql) === TRUE) {
//     echo "Account updated successfully";

//     // Add Transaction Log
//     $transaction_type = 'Wallet';
//     $description = 'Lochana Wallet';
//     transaction_log($transaction_type, $description, -$amount);
// } else {
//     echo "Error: " . $sql . "<br>" . $con->error;
// }
// $con->close();

// 2. Send Database Backup to Email
$server = 'morgana.webserverlive.com';
$db_user = 'srijayal_system';
$db_pwd = 'system@123456';
$db_name = 'srijayal_system';
// Email details
$to = "induwarauthsara19@gmail.com, lochana587@gmail.com, srijayaprint@gmail.com";
$subject = date('Y-m-d') . " Srijaya Billing System Database Backup";
$message = "Please find attached the database backup for " . date('Y-m-d');
$headers = "From: no-reply@yourdomain.com"; // Replace with your email address

// Command to execute mysqldump and capture output
$command = "mysqldump --opt -h $server -u $db_user -p$db_pwd $db_name";
$output = shell_exec($command);
echo "<pre>$output</pre>";

// Check if the command was successful
if ($output !== null) {
    // Prepare the output as an attachment
    $encoded_content = chunk_split(base64_encode($output));
    $uid = md5(uniqid(time()));

    // Headers for attachment
    $headers .= "\r\nMIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$uid\"\r\n\r\n";
    $headers .= "This is a multi-part message in MIME format.\r\n";
    $headers .= "--$uid\r\n";
    $headers .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $headers .= $message . "\r\n\r\n";
    $headers .= "--$uid\r\n";
    $headers .= "Content-Type: application/octet-stream; name=\"backup.sql\"\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";
    $headers .= "Content-Disposition: attachment; filename=\"backup.sql\"\r\n\r\n";
    $headers .= $encoded_content . "\r\n\r\n";
    $headers .= "--$uid--";

    // Send the email
    if (mail($to, $subject, "", $headers)) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email.";
    }
} else {
    echo "Database backup failed.";
}