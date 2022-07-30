<?php require_once '../../inc/config.php'; ?>
<?php require_once '../../inc/header.php';
include '../auth.php' ?>


<?php
if (isset($_GET['id'])) {
    global $con;
    $item_id = $_GET['id'];
    $sql = "SELECT * FROM items WHERE id = $item_id";
    $result =   mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $output = mysqli_fetch_assoc($result);
        /*echo "<pre>"; print_r($output); echo "</pre>";*/
        $item_id = $output['id'];
        $item_name = $output['item_name'];
        $item_description = $output['description'];
        $item_qty = $output['qty'];
        $item_cost = $output['cost'];
        $item_supplier = $output['supplier'];
    } else {
        echo "<p class='error'> You Entered Item can't find in Database. <button> <a href='new.php'> Add New Item</a></button> </p>";
    }
}
if (isset($_GET['item'])) {
    global $con;
    $item_name = $_GET['item'];
    $sql = "SELECT * FROM items WHERE item_name = '$item_name'";
    $result =   mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $output = mysqli_fetch_assoc($result);
        /*  echo "<pre>"; print_r($output); echo "</pre>";*/
        $item_id = $output['id'];
        $item_name = $output['item_name'];
        $item_description = $output['description'];
        $item_qty = $output['qty'];
        $item_cost = $output['cost'];
        $item_supplier = $output['supplier'];
    } else {
        echo "<p class='error'> You Entered Item can't find in Database. <button> <a href='new.php'> Add New Item</a></button> </p>";
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
    <title>Modify Item</title>
</head>

<body>
    <div class="content-wrapper">
        <?php require_once 'menu.php'; ?>
        <h1><U>Modify Item</U></h1>
        <div class="select_item">
            <fieldset>
                <legend>Search Item</legend>
                <form action="" method="GET">
                    <div class="form_field"><label for="select_item">
                            Select Item:</label>
                        <input name="tab" value="modify" style="display:none;">
                        <input list="item_list" type="text" name="item" <?php if (!isset($item_id)) {
                                                                            echo "autofocus";
                                                                        } ?>>
                    </div>
                    <div class="form_field">
                        <input type="submit" value="Search Item" id="submit-btn">
                    </div>
                </form>
            </fieldset>
        </div>
        <br>
        <form action="" method="POST">
            <fieldset>
                <legend>Modify <?php if (isset($item_name)) {
                                    echo $item_name;
                                }  ?> Item data :</legend>
                <div class="field"><label for="item_id">Item ID :</label>
                    <input type="text" name="item_id" disabled required value="<?php if (isset($item_id)) {
                                                                                    echo $item_id;
                                                                                }  ?>">
                </div>
                <div class="field"><label for="item_name">Item Name :</label>
                    <input type="text" name="item_name" required value="<?php if (isset($item_name)) {
                                                                            echo $item_name;
                                                                        } ?>">
                </div>

                <div class="field"><label for="item_description">Description :</label>
                    <input type="text" name="item_description" value="<?php if (isset($item_description)) {
                                                                            echo $item_description;
                                                                        }  ?>">
                </div>

                <div class="field"><label for="item_qty">QTY :</label>
                    <input type="number" name="item_qty" step="any" min="0" required value="<?php if (isset($item_qty)) {
                                                                                                echo $item_qty;
                                                                                            }  ?>">
                </div>

                <div class="field"><label for="item_cost">Cost :</label>
                    <input type="number" name="item_cost" step="any" min="0" required value="<?php if (isset($item_cost)) {
                                                                                                    echo $item_cost;
                                                                                                }  ?>">
                </div>

                <div class="field"><label for="item_supplier">Supplier :</label>
                    <input type="text" name="item_supplier" value="<?php if (isset($item_supplier)) {
                                                                        echo $item_supplier;
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

<!-- Send Data to Modify Item -->
<?php
// Check Submit Button Cliecked
if (isset($_POST['submit'])) {
    // Set Add Item values
    global $item_id;
    $item_name = $_POST['item_name'];
    $item_description = $_POST['item_description'];
    $item_qty = $_POST['item_qty'];
    $item_cost = $_POST['item_cost'];
    $item_supplier = $_POST['item_supplier'];

    // Add New Item
    $sql = "UPDATE `items` SET `item_name`='{$item_name}',`description`='{$item_description}',`cost`='{$item_cost}',`qty`='{$item_qty}',`supplier`='{$item_supplier}' WHERE id = {$item_id}";
    insert_query($sql, "Successfully Modified <b>{$item_name}</b> Item!");

    // Refresh Page
    $this_url = basename($_SERVER["SCRIPT_FILENAME"]);
    /*header("refresh:2; url={$this_url}");*/
    echo "<script>
    setTimeout(`location.href = '$this_url';`, 3000);
    </script> ";
}
?>

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

<?php include '../../inc/footer.php'; ?>