<?php

require_once '../inc/config.php'; // Include the database configuration file

header('Content-Type: application/json'); // Set the content type to JSON

$response = array(); // Initialize an array to hold the response data

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && isset($_POST['emp_id'])) {
    $emp_id = intval($_POST['emp_id']);
    $data = file_get_contents($_FILES['file']['tmp_name']);

    // Validate file type and size
    $allowed_types = ['image/jpeg', 'image/png'];
    $file_type = $_FILES['file']['type'];
    $file_size = $_FILES['file']['size'];

    if (!in_array($file_type, $allowed_types)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid file type. Only JPG and PNG are allowed.';
    } elseif ($file_size > 1000000) { // 1MB
        $response['status'] = 'error';
        $response['message'] = 'File size exceeds 1MB.';
    } else {
        if ($stmt = $con->prepare("UPDATE employees SET dp = ? WHERE employ_id = ?")) {
            $stmt->bind_param("bi", $null, $emp_id);
            $stmt->send_long_data(0, $data);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Image uploaded successfully.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to prepare the SQL statement.';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request or no file uploaded.';
}

echo json_encode($response); // Output the response as JSON
?>