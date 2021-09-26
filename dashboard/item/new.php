<?php require_once '../../inc/config.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard-page.css">
    <title>Add Item</title>
</head>

<body>
    <div class="content-wrapper">
        <h1><U>Add New Item</U></h1>

        <form action="" method="POST">
            <fieldset>
                <legend>Add New Item data :</legend>
                <div class="field"><label for="item_id">Item ID :</label>
                    <input type="text" name="item_id" required value="<?php $last_no = "SELECT MAX(id) FROM items";
                                                                        $result = mysqli_query($con, $last_no);
                                                                        $output = mysqli_fetch_assoc($result);
                                                                        $new_item_id = $output['MAX(id)'] + 1;
                                                                        echo "{$new_item_id}"; ?>">
                </div>
                <div class="field"><label for="item_name">Item Name :</label>
                    <input type="text" name="item_name" required autofocus>
                </div>

                <div class="field"><label for="item_description">Description :</label>
                    <input type="text" name="item_description">
                </div>

                <div class="field"><label for="item_qty">QTY :</label>
                    <input type="number" name="item_qty" step="any" min="0">
                </div>

                <div class="field"><label for="item_cost">Cost (per Unit) :</label>
                    <input type="number" name="item_cost" step="any" min="0" required>
                </div>

                <div class="field"><label for="item_supplier">Supplier :</label>
                    <input type="text" name="item_supplier">
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

<!-- Send Data to Add Item -->
<?php
// Check Submit Button Cliecked
if (isset($_POST['submit'])) {
    // Set Add Item values
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_description = $_POST['item_description'];
    $item_qty = $_POST['item_qty'];
    $item_cost = $_POST['item_cost'];
    $item_supplier = $_POST['item_supplier'];

    // Add New Item
    $sql = "INSERT INTO items (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`)
                        VALUES ('{$item_id}', '{$item_name}', '{$item_description}', {$item_cost}, {$item_qty}, '{$item_supplier}');";
    insert_query($sql, "Successfully Added <b>{$item_name}</b> as New Item!");

    // Fall Stock Account when Item Buy
    $added_item_cost = $item_qty * $item_cost;
    $account_name = "Stock Account";
    $sql = "UPDATE `accounts` SET `amount`= amount - {$added_item_cost} WHERE account_name = '{$account_name}'";
    insert_query($sql, "Successfully Modified <b>{$item_name}</b> Item!");

    // Refresh Page
    $this_url = basename($_SERVER["SCRIPT_FILENAME"]);
    header("refresh:2; url={$this_url}");
}
?>
<?php end_db_con(); ?>