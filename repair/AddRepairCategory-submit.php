<?php require_once '../inc/config.php';

// Get the category name from the request
if (isset($_GET['categoryName'])) {
    $categoryName = trim($_GET['categoryName']);

    // Prepare and bind
    $stmt = $con->prepare("INSERT INTO repair_categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $categoryName);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Category added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Category name is required.";
}

// Close the connection
$con->close();
?>