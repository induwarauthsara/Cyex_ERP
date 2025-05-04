<?php require_once '../inc/config.php'; ?>
<a href="javascript:history.back()"><button style="margin:15px;">Go Back</button></a>
<?php

// COMBO PRODUCT QUANTITY UPDATER
// This script automatically updates the quantity of combo products
// based on the available quantities of their components

echo "<h2>Combo Product Quantity Updater</h2>";
echo "<pre>";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Get all combo products
$comboQuery = "SELECT p.product_id, p.product_name 
               FROM products p 
               WHERE p.product_type = 'combo'";

$comboResult = $con->query($comboQuery);

if (!$comboResult) {
    die("Error fetching combo products: " . $con->error);
}

$updatedCount = 0;
$skippedCount = 0;
$errorCount = 0;

while ($comboProduct = $comboResult->fetch_assoc()) {
    $comboId = $comboProduct['product_id'];
    $comboName = $comboProduct['product_name'];
    
    echo "Processing combo product: {$comboName} (ID: {$comboId})\n";
    
    // Get all components for this combo product
    $componentsQuery = "SELECT cp.component_product_id, cp.quantity as required_qty, 
                        p.product_name as component_name
                        FROM combo_products cp 
                        JOIN products p ON p.product_id = cp.component_product_id 
                        WHERE cp.combo_product_id = ?";
    
    $stmt = $con->prepare($componentsQuery);
    if (!$stmt) {
        echo "  Error preparing components query: " . $con->error . "\n";
        $errorCount++;
        continue;
    }
    
    $stmt->bind_param("i", $comboId);
    $stmt->execute();
    $componentsResult = $stmt->get_result();
    
    if ($componentsResult->num_rows === 0) {
        echo "  No components found for this combo product. Skipping.\n";
        $skippedCount++;
        continue;
    }
    
    // Calculate maximum possible quantity
    $maxQuantity = null;
    $limitingComponent = "";
    
    while ($component = $componentsResult->fetch_assoc()) {
        $componentId = $component['component_product_id'];
        $requiredQty = floatval($component['required_qty']);
        $componentName = $component['component_name'];
        
        // Get available quantity for this component
        $stockQuery = "SELECT SUM(pb.quantity) as total_quantity 
                       FROM product_batch pb 
                       WHERE pb.product_id = ? AND pb.status = 'active'
                       GROUP BY pb.product_id";
        
        $stockStmt = $con->prepare($stockQuery);
        if (!$stockStmt) {
            echo "  Error preparing stock query: " . $con->error . "\n";
            continue;
        }
        
        $stockStmt->bind_param("i", $componentId);
        $stockStmt->execute();
        $stockResult = $stockStmt->get_result();
        
        if ($row = $stockResult->fetch_assoc()) {
            $availableQty = floatval($row['total_quantity']);
            
            // Calculate how many combo units can be created from this component
            $possibleComboUnits = floor($availableQty / $requiredQty);
            
            echo "  Component: {$componentName}, Available: {$availableQty}, Required: {$requiredQty}, Possible units: {$possibleComboUnits}\n";
            
            // Update the max quantity if this is lower than the current max
            if ($maxQuantity === null || $possibleComboUnits < $maxQuantity) {
                $maxQuantity = $possibleComboUnits;
                $limitingComponent = $componentName;
            }
        } else {
            // If any component has zero stock, the combo can't be created
            echo "  Component {$componentName} has no available stock. Cannot create any combo units.\n";
            $maxQuantity = 0;
            $limitingComponent = $componentName;
            break;
        }
        
        $stockStmt->close();
    }
    
    // Default to 0 if no quantity could be calculated
    $maxQuantity = ($maxQuantity !== null) ? $maxQuantity : 0;
    
    echo "  Maximum possible quantity: {$maxQuantity}" . ($limitingComponent ? " (Limited by: {$limitingComponent})" : "") . "\n";
    
    // Update all batches for this combo product
    $updateQuery = "UPDATE product_batch SET quantity = ? WHERE product_id = ?";
    $updateStmt = $con->prepare($updateQuery);
    
    if (!$updateStmt) {
        echo "  Error preparing update query: " . $con->error . "\n";
        $errorCount++;
        continue;
    }
    
    $updateStmt->bind_param("di", $maxQuantity, $comboId);
    
    if ($updateStmt->execute()) {
        $affectedRows = $updateStmt->affected_rows;
        echo "  Updated {$affectedRows} batch(es) with new quantity: {$maxQuantity}\n";
        $updatedCount++;
    } else {
        echo "  Error updating batches: " . $updateStmt->error . "\n";
        $errorCount++;
    }
    
    $updateStmt->close();
    echo "\n";
}

echo "\nSummary:\n";
echo "  Processed combo products: " . $comboResult->num_rows . "\n";
echo "  Successfully updated: {$updatedCount}\n";
echo "  Skipped: {$skippedCount}\n";
echo "  Errors encountered: {$errorCount}\n";
echo "\nCompleted at: " . date('Y-m-d H:i:s') . "\n";
echo "</pre>";

// Close the database connection
$con->close();
?>
