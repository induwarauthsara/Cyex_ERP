<?php
require_once '../inc/config.php'; 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product']) && !empty($_POST['product'])) {
        $product_name = $_POST['product'];

        // Prepare and execute the SQL query
        $stmt = $con->prepare("SELECT commission, cost, selling_price FROM repair_items WHERE repair_name = ?");
        $stmt->bind_param("s", $product_name);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the product is found
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'commission' => $row['commission'],
                'cost' => $row['cost'],
                'selling_price' => $row['selling_price']
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No product found']);
        }

        $stmt->close();
        $con->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid product']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
