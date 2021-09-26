<?php require_once '../../inc/config.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard-page.css">
    <title>Build Product</title>
</head>

<body>
    <div class="content-wrapper">
        <h1><U>Build New Product</U></h1>

        <form action="" method="POST">
            <fieldset>
                <legend>Build New Product data :</legend>
                <div class="field"><label for="product_id">Product ID :</label>
                    <input type="text" name="product_id" required value="<?php $last_no = "SELECT MAX(product_id) FROM products";
                                                                            $result = mysqli_query($con, $last_no);
                                                                            $output = mysqli_fetch_assoc($result);
                                                                            $new_product_id = $output['MAX(product_id)'] + 1;
                                                                            echo "{$new_product_id}"; ?>">
                </div>

                <div class="field"><label for="product_name">Product Name :</label>
                    <input type="text" name="product_name" required>
                </div>

                <div class="field"><label for="description">Description :</label>
                    <input type="text" name="description">
                </div>

                <div class="field"><label for="rate">Rate :</label>
                    <input type="number" name="rate" min=1 step=0.01 required>
                </div>

                <div class="field submit">
                    <input type="reset" id="reset-btn" oncliclk="location.reload()">
                    <input type="submit" name="submit" id="submit-btn">
                </div>
            </fieldset>
        </form>
    </div>
</body>

</html>

<!-- Send Data to Build Product -->
<?php
// Check Submit Button Cliecked
if (isset($_POST['submit'])) {
    // Set Build Product values
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['description'];
    $product_rate = $_POST['rate'];

    // Build New Product
    $sql = "INSERT INTO `products`(`product_id`, `product_name`, `description`,  `rate`) 
    VALUES ('{$product_id}','{$product_name}','{$product_description}', {$product_rate}')";
    insert_query($sql, "Successfully Builded <b>{$product_name}</b> as New Product!");

    // Refresh Page
    $this_url = basename($_SERVER["SCRIPT_FILENAME"]);
    header("refresh:2; url={$this_url}");
}
?>
<?php end_db_con(); ?>