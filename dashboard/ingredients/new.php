<?php require_once '../../inc/config.php'; ?>

<?php $tabel_name = "ingredients"; ?>

<?php if (isset($_GET['product'])) {
    $product = $_GET['product'];
    $add = $_GET['add'];
} else {
    $product = "";
    $add = "1";
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard-page.css">
    <title>New</title>
</head>

<body>
    <div class="content-wrapper">
        <h1><U>Product to Item(s)</U></h1>
        <fieldset>
            <legend>Select a product</legend>
            <form action="" method="GET" id="search">
                <input name="tab" value="new" style="display:none;" />
                <div class="form_field"><label for="ingredient_id">Ingredient ID :</label>
                    <input type="text" name="ingredient_id" required value="<?php $last_no = "SELECT MAX(id) FROM {$tabel_name}";
                                                                            $result = mysqli_query($con, $last_no);
                                                                            $output = mysqli_fetch_assoc($result);
                                                                            $new_product_id = $output['MAX(id)'] + 1;
                                                                            echo "{$new_product_id}"; ?>">
                </div>
                <div class="form_field"> <label for="select_product"> Select Product </label>
                    <input list="product_list" type="text" name="product" value="<?php echo $product; ?>" <?php if (!isset($_GET['product'])) {
                                                                                                                echo "autofocus";
                                                                                                            } ?> />
                </div>

                <div class="form_field"> <label for="add"> Ingredient Count:</label>
                    <input type="number" name="add" value="<?php echo $add; ?>" />
                </div>

                <div class="form_field"> <input type="submit" value="Make Ingredient" id="submit-btn">
                </div>
            </form>
        </fieldset>
        <br />

        <form action="" method="POST">
            <fieldset>
                <legend> <?php echo $product; ?> Product Ingredient :</legend>
                <div style="display:none;">


                    <div class="field"><label for="product_name">Product Name :</label>
                        <input list="product_list" type="text" name="product_name" required value="<?php echo $product; ?>" />
                    </div>
                </div>

                <?php global $add;
                for ($i = 0; $add > $i; $i++) {
                    $ii = $i + 1;
                    echo "
                    <div class='field' id='item_name_{$i}'><label for='ingridian_{$i}'>Item {$ii} Name :</label>
                    <input list='item_list' type='text' name='item_{$i}' required /> </div>

                    <div class='field' id='item_qty_{$i}'><label for='product_qty'>Item {$ii} QTY :</label>
                    <input type='number' name='qty_{$i}' min='0.001' required /> </div> 
                   <button id='item_remove_{$i}' onClick='remove_ingd_item({$i});' style='margin-left:200px; color:red; margin-top:-5px;'>Remove</button>
                    <hr id='item_hr_{$i}'/>";
                } ?>
                <div class="field submit">
                    <input type="reset" id="reset-btn" oncliclk="location.reload()">
                    <input type="submit" name="submit" id="submit-btn">
                </div>
            </fieldset>
        </form>
    </div>
</body>
<script>
    function remove_ingd_item(id) {
        // Variables
        remove_name_div = document.getElementById("item_name_" + id).remove();
        remove_qty_div = document.getElementById("item_qty_" + id).remove();
        remove_button = document.getElementById("item_remove_" + id).remove();
        remove_hr = document.getElementById("item_hr_" + id).remove();
    }
</script>

</html>

<!-- Send Data to Build Ingridiants -->
<?php
// Check Submit Button Cliecked
if (isset($_POST['submit'])) {
    // Set Build Product values
    $ingredient_id = $_POST['ingredient_id'];
    $ingredient_id--;
    $product_name = $_POST['product_name'];
    global $add;

    // Build New Ingridiants
    for ($i = 0; $add > $i; $i++) {
        global $ingredient_id;
        global $product_name;
        global $tabel_name;

        $ingredient_id++;
        $item_name = $_POST["item_{$i}"];
        $item_qty = $_POST["qty_{$i}"];

        $sql = "INSERT INTO `{$tabel_name}` (`id`, `item_name`, `product_name`, `qty`) 
    VALUES ('{$ingredient_id}','{$item_name}','{$product_name}','{$item_qty}')";
        insert_query($sql, "Successfully added {$product_name} to {$item_name} x{$item_qty} as New Ingridiants! <br> <br>");
    }
}
?>

<!-- == product list - Data List get from Database == -->
<datalist id="product_list">
    <!-- == products == -->
    <?php $product_list = "SELECT product_name FROM products";
    $result = mysqli_query($con, $product_list);
    if ($result) {
        echo "<ol>";
        while ($recoard = mysqli_fetch_assoc($result)) {
            $product = $recoard['product_name'];
            echo "<option value='{$product}'>";
        }
        echo "</ol>";
    } else {
        echo "<option value='Result 404'>";
    }
    ?>
</datalist>

<!-- == Item list - Data List get from Database == -->
<datalist id="item_list">
    <!-- == Items == -->
    <?php $item_list = "SELECT item_name FROM items";
    $result = mysqli_query($con, $item_list);
    if ($result) {
        echo "<ol>";
        while ($recoard = mysqli_fetch_assoc($result)) {
            $item = $recoard['item_name'];
            echo "<option value='{$item}'>";
        }
        echo "</ol>";
    } else {
        echo "<option value='Result 404'>";
    }
    ?>
</datalist>

<?php end_db_con(); ?>