<?php require_once '../../inc/config.php'; ?>


<?php
$iwara_na = "Meka hadala iwara na..";
if (isset($_GET['id'])) {
    global $con;
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result =   mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $output = mysqli_fetch_assoc($result);
        /*echo "<pre>"; print_r($output); echo "</pre>";*/
        $product_id = $output['product_id'];
        $product_name = $output['product_name'];
        $product_description = $output['description'];
        $product_qty = $output['stock_qty'];
        $product_rate = $output['rate'];
        $product_cost = $output['cost'];
        $product_profit = $output['profit'];
        $product_has_stock = $output['has_stock'];
    } else {
        echo "You Entered product can't find in Database. <button> <a href='new.php'> Add New product</a></button>";
    }
}
if (isset($_GET['product'])) {
    global $con;
    $product_name = $_GET['product'];
    $sql = "SELECT * FROM products WHERE product_name = '$product_name'";
    $result =   mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $output = mysqli_fetch_assoc($result);
        /*  echo "<pre>"; print_r($output); echo "</pre>";*/
        $product_id = $output['product_id'];
        $product_name = $output['product_name'];
        $product_description = $output['description'];
        $product_qty = $output['stock_qty'];
        $product_rate = $output['rate'];
        $product_cost = $output['cost'];
        $product_profit = $output['profit'];
        $product_has_stock = $output['has_stock'];
    } else {
        echo "You Entered product can't find in Database. <button> <a href='new.php'> Add New product</a></button>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard-page.css">
    <title>Modify Product</title>
</head>

<body>
    <div class="content-wrapper">
        <h1><U><?php echo $iwara_na /*Modify Product*/ ?></U></h1>
        <div class="select_product">
            <fieldset>
                <legend>Search product</legend>
                <form action="" method="GET">
                    <div class="form_field"><label for="product">
                            Select product:</label>
                        <input name="tab" value="modify" style="display:none;">
                        <input list="product_list" type="text" name="product" <?php if (!isset($product_id)) {
                                                                                    echo "autofocus";
                                                                                } ?>>
                    </div>
                    <div class="form_field">
                        <input type="submit" value="Search product" id="submit-btn">
                    </div>
                </form>
            </fieldset>
        </div>
        <br>
        <form action="" method="POST">
            <fieldset>
                <legend>Modify <?php if (isset($product_name)) {
                                    echo $product_name;
                                }  ?> product data :</legend>
                <div class="field"><label for="product_id">product ID :</label>
                    <input type="text" name="product_id" disabled required value="<?php if (isset($product_id)) {
                                                                                        echo $product_id;
                                                                                    }  ?>">
                </div>
                <div class="field"><label for="product_name">Product Name :</label>
                    <input type="text" name="product_name" required value="<?php if (isset($product_name)) {
                                                                                echo $product_name;
                                                                            } ?>">
                </div>

                <div class="field"><label for="description">Description :</label>
                    <input type="text" name="description" value="<?php if (isset($product_description)) {
                                                                        echo $product_description;
                                                                    }  ?>">
                </div>

                <div class="field"><label for="product_qty">QTY :</label>
                    <input type="number" name="product_qty" required value="<?php if (isset($product_qty)) {
                                                                                echo $product_qty;
                                                                            }  ?>">
                </div>

                <div class="field"><label for="rate">Rate :</label>
                    <input type="number" name="rate" min=1 step=0.01 required value="<?php if (isset($product_rate)) {
                                                                                            echo $product_rate;
                                                                                        }  ?>">
                </div>

                <div class="field"><label for="cost">Cost :</label>
                    <input type="number" name="cost" min=0.1 step=0.01 required value="<?php if (isset($product_cost)) {
                                                                                            echo $product_cost;
                                                                                        }  ?>">
                </div>

                <div class="field"><label for="profit">Profit :</label>
                    <input type="number" name="profit" min=0.1 step=0.01 required value="<?php if (isset($product_profit)) {
                                                                                                echo $product_profit;
                                                                                            }  ?>">
                </div>

                <div class="field"><label for="has_stock">Has Stock :</label>
                    <input type="text" name="has_stock" required value="<?php if (isset($product_has_stock)) {
                                                                            echo $product_has_stock;
                                                                        }  ?>">
                </div>

                <div class="field submit">
                    <input type="reset" id="reset-btn" oncliclk="location.reload()">
                    <input type="submit" name="submit" id="submit-btn" value="Modify">
                </div>
            </fieldset>
        </form>
    </div>
</body>

</html>

<!-- Send Data to Modify product -->
<?php
// Check Submit Button Cliecked
if (isset($_POST['submit'])) {
    // Set Add product values
    global $product_id;
    $product_name = $_POST['product_name'];
    $product_description = $_POST['description'];
    $product_qty = $_POST['product_qty'];
    $product_rate = $_POST['rate'];
    $product_cost = $_POST['cost'];
    $product_profit = $_POST['profit'];
    $product_has_stock = $_POST['has_stock'];

    // Add New product
    $sql = "UPDATE `products` SET `product_id`='{$product_id}', `product_name`='{$product_name}',`description`='{$product_description}',
    `stock_qty`='{$product_qty}',`rate`='{$product_rate}',`cost`='{$product_cost}',`profit`='{$product_profit}',`has_stock`='{$product_has_stock}' WHERE product_id = {$product_id}";
    insert_query($sql, "Successfully Modified <b>{$product_name}</b> product!");

    // Refresh Page
    $this_url = basename($_SERVER["SCRIPT_FILENAME"]);
    header("refresh:2; url={$this_url}");
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

<?php end_db_con(); ?>