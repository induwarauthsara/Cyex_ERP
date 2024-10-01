<?php
require_once '../inc/config.php';

if (isset($_POST['repairName'])) {
    $repairName = $_POST['repairName'];

    // Query to check if the repair name exists in the database
    $query = "SELECT * FROM repair_items WHERE repair_name = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $repairName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Repair name exists
        echo 'exists';
    } else {
        // Repair name does not exist
        echo 'not_exists';
    }

    $stmt->close();
    $con->close();
}
