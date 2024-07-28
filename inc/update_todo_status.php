<?php require_once 'config.php';

// Check if the todoId parameter is set in the GET request
if (isset($_GET['todoId'])) {
    // Sanitize and retrieve the todoId from the GET request
    $todoId = mysqli_real_escape_string($con, $_GET['todoId']);

    // Update the status of the todo item in the database
    $sql = "UPDATE `todo` SET `status` = 0 WHERE `todo_id` = '$todoId'";
    insert_query($sql, "Todo ID: $todoId", "Update Todo Item Status");

    if ($result) {
        // Return a success message if the update was successful
        echo "Todo item status updated successfully.";
    } else {
        // Return an error message if the update failed
        echo "Error: Unable to update todo item status.";
    }
} else {
    // Return an error message if the todoId parameter is not set
    echo "Error: Missing todoId parameter.";
}
