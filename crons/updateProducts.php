<?php require_once '../../inc/config.php'; ?>
<a href="javascript:history.back()"><button style="margin:15px;">Go Back</button></a>
<?php
// Get Product List to Array
$product_list_array = array();
$select_product_list_sql = "SELECT product_name FROM products;";

if ($result = mysqli_query($con, $select_product_list_sql)) {
    while ($row = mysqli_fetch_array($result)) {
        array_push($product_list_array, $row["product_name"]);
    }
    mysqli_free_result($result);
}


for ($i = 0; $i < count($product_list_array); $i++) {

    $product = $product_list_array[$i];

    $sql = "SELECT rate FROM products WHERE product_name = '{$product}';";
    $result = mysqli_query($con, $sql);
    $recoard = mysqli_fetch_assoc($result);
    $product_rate = $recoard["rate"];

    // Product Qty
    // -------- Get Product ingredients to Array --------
    $sql = "SELECT item_name FROM ingredients WHERE product_name='{$product}'";
    $result = mysqli_query($con, $sql);
    $ingridians_list = array();
    if (mysqli_num_rows($result) > 0) {
        while ($recoard = mysqli_fetch_assoc($result)) {
            array_push($ingridians_list, $recoard["item_name"]);
        }
    }
    // -------- Get QTY of ingridians_list to Array --------
    $ingridians_requement_qty_array = array();
    $item_qty_array = array();
    $makeable_product_qty_array = array();

    for ($i = 0; $i < count($ingridians_list); $i++) {
        $selected_item = $ingridians_list[$i];

        $selected_item_req_qty_sql = "SELECT qty FROM ingredients WHERE item_name = '{$selected_item}' AND product_name = '{$product}';";
        $result = mysqli_query($con, $selected_item_req_qty_sql);
        $recoard = mysqli_fetch_assoc($result);
        array_push($ingridians_requement_qty_array, $recoard["qty"]);

        $selected_item_qty_sql = "SELECT qty FROM items WHERE item_name = '{$selected_item}';";
        $result = mysqli_query($con, $selected_item_qty_sql);
        $recoard = mysqli_fetch_assoc($result);
        array_push($item_qty_array, $recoard["qty"]);

        $selected_item_ingridians_requement = $ingridians_requement_qty_array[$i];
        $selected_item_qty = $item_qty_array[$i];
        $makeable_product_qty = $selected_item_qty / $selected_item_ingridians_requement;
        array_push($makeable_product_qty_array, $makeable_product_qty);
    }
    // -------- Select Min QTY of ingridians_qty --------
    $min_ingridians_qty = min($makeable_product_qty_array);
    // -------- Set Product QTY = $min_ingridians_qty --------
    $product_qty = $min_ingridians_qty;

    // Product Cost
    $product_cost = 0;
    for ($i = 0; $i < count($ingridians_list); $i++) {
        $selected_item = $ingridians_list[$i];
        $selected_item_cost_sql = "SELECT cost FROM items WHERE item_name = '{$selected_item}'";
        $result = mysqli_query($con, $selected_item_cost_sql);
        $recoard = mysqli_fetch_assoc($result);
        $selected_item_cost = $recoard["cost"];
        $product_cost += $selected_item_cost;
    }
    echo "<br>Final Cost :  {$product_cost} <br>";


    // Product Profit
    $product_profit = $product_rate - $product_cost;

    // Has Product in Stock ?
    if ($product_qty > 0) {
        $product_has_stock = 1;
    } else {
        $product_has_stock = 0;
    }

    // Update Product Data
    $sql = "UPDATE `products` SET 
stock_qty='{$product_qty}', cost='{$product_cost}', profit='{$product_profit}', has_stock='{$product_has_stock}' 
WHERE product_name='{$product}'";
    insert_query($sql, "Successfully Update <b> {$product}</b> Product!");
}

?>