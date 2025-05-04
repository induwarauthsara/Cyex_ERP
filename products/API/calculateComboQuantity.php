<?php
require_once '../../inc/config.php';
header('Content-Type: application/json');

/**
 * Calculate the maximum available quantity for a combo product based on its components
 * 
 * @param array $comboComponents Array of component data with productId and quantity
 * @return int The maximum number of combo products that can be created
 */
function calculateComboQuantity($comboComponents) {
    global $con;
    
    $maxQuantity = null;
    
    foreach ($comboComponents as $component) {
        if (empty($component['productId']) || empty($component['quantity']) || $component['quantity'] <= 0) {
            continue;
        }
        
        // Get available quantity for this component
        $query = "SELECT pb.product_id, SUM(pb.quantity) as total_quantity 
                  FROM product_batch pb 
                  WHERE pb.product_id = ? AND pb.status = 'active'
                  GROUP BY pb.product_id";
        
        $stmt = $con->prepare($query);
        if (!$stmt) {
            return 0; // Return 0 if there's an error
        }
        
        $stmt->bind_param("i", $component['productId']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $availableQty = floatval($row['total_quantity']);
            $requiredQty = floatval($component['quantity']);
            
            // Calculate how many combo units can be created from this component
            $possibleComboUnits = floor($availableQty / $requiredQty);
            
            // Update the max quantity if this is lower than the current max
            if ($maxQuantity === null || $possibleComboUnits < $maxQuantity) {
                $maxQuantity = $possibleComboUnits;
            }
        } else {
            // If any component has zero stock, the combo can't be created
            return 0;
        }
        
        $stmt->close();
    }
    
    return $maxQuantity !== null ? $maxQuantity : 0;
}

// Process API request when this file is accessed directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (!$inputData || !isset($inputData['comboComponents']) || !is_array($inputData['comboComponents'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input data'
        ]);
        exit;
    }

    $comboComponents = $inputData['comboComponents'];
    
    // Calculate quantity
    $maxQuantity = calculateComboQuantity($comboComponents);

    // Return the calculated quantity
    echo json_encode([
        'success' => true,
        'quantity' => $maxQuantity,
        'message' => 'Combo quantity calculated successfully'
    ]);
} 