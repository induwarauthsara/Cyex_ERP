<?php require_once '../../inc/config.php';
include '../auth.php' ?>


<?php
$ingridians_id = array();
$ingridians_list = array();
$ingridians_count = array();

if (isset($_GET['id'])) {
    global $con;
    $ingredient_id = $_GET['id'];
    $sql = "SELECT * FROM makeProduct WHERE id = $ingredient_id";
    $result =   mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $output = mysqli_fetch_assoc($result);
        /*echo "<pre>"; print_r($output); echo "</pre>";*/
        $ingredient_id = $output['id'];
        $item_name = $output['item_name'];
        $product_name = $output['product_name'];
        $qty = $output['qty'];
        $count = "1";
        array_push($ingridians_id, $output["id"]);
        array_push($ingridians_list, $output["item_name"]);
        array_push($ingridians_count,  $output["qty"]);
    } else {
        echo "You Entered Product can't find in Database. <button> <a href='?tab=new'> Add Ingredient to New Product</a></button>";
    }
}

if (isset($_GET['product'])) {
    global $con;
    $product_name = $_GET['product'];
    $sql = "SELECT * FROM makeProduct WHERE product_name = '$product_name'";
    $result =   mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $output = mysqli_fetch_assoc($result);
        /*  echo "<pre>"; print_r($output); echo "</pre>";*/
        $ingredient_id = $output['id'];
        $item_name = $output['item_name'];
        $product_name = $output['product_name'];
        $qty = $output['qty'];

        // Ingredient List Count
        $item_list_sql = "SELECT * FROM makeProduct WHERE product_name='{$product_name}'";
        $item_list_result = mysqli_query($con, $item_list_sql);

        if (mysqli_num_rows($result) > 0) {
            while ($recoard = mysqli_fetch_assoc($item_list_result)) {
                // $ingridians_list += [$recoard["item_name"] => $recoard["qty"]];
                array_push($ingridians_id, $recoard["id"]);
                array_push($ingridians_list, $recoard["item_name"]);
                array_push($ingridians_count,  $recoard["qty"]);
            }
        }
        $count = count($ingridians_list);
    } else {
        echo "You Entered Product can't find in Database. <button> <a href='?tab=new'> Add Ingredient to New Product</a></button>";
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
        <h1><U>
                <center> Edit Make Product </center>
            </U></h1>
        <div class="select_product">
            <fieldset>
                <legend>Search product Ingredient</legend>
                <form action="" method="GET">

                    <div class="form_field"><label for="product">
                            Select product:</label>
                        <input name="tab" value="modify" style="display:none;">
                        <input list="product_list" type="text" name="product" <?php if (!isset($product_name)) {
                                                                                    echo "autofocus";
                                                                                } else {
                                                                                    echo "value='$product_name'";
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
                <legend>Edit <u> <?php if (isset($product_name)) {
                                        echo $product_name;
                                    }  ?></u> Ingredient :</legend>

                <?php global $count;
                for ($i = 0; $count > $i; $i++) {
                    $ii = $i + 1;
                    echo "
                    <div class='field' id='ingredient_{$i}_id'><label for='ingridian_{$i}_id'>Ing. {$ii} Id :</label>
                    <input type='text' name='id_{$i}' value='$ingridians_id[$i]' required /> </div>


                    <div class='field' id='item_name_{$i}'><label for='ingridian_{$i}'>Item {$ii} Name :</label>
                    <input list='item_list' type='text' name='item_{$i}' value='$ingridians_list[$i]' required /> </div>

                    <div class='field' id='item_qty_{$i}'><label for='product_qty'>Item {$ii} QTY :</label>
                    <input type='number' id='i_item_qty_{$i}' name='qty_{$i}' min='0' value='$ingridians_count[$i]' required /> </div> 

                   <div id='item_remove_{$i}' onClick='remove_ingd_item({$i});' style='margin-left:200px; color:red; margin-top:-5px; cursor:pointer; border: 1px solid black; width:4rem; padding:3px; background-color: lightgray;'>Remove</div>
                   <input id='deleted_{$i}' name='deleted_{$i}' value='no' hidden />
                    <hr id='item_hr_{$i}'/>";
                } ?>

                <div class="field submit">
                    <input type="reset" id="reset-btn" oncliclk="location.reload()">
                    <input type="submit" name="submit" id="submit-btn" value="Modify">
                </div>
            </fieldset>
        </form>
    </div>
</body>
<script>
    function remove_ingd_item(id) {
        // Variables

        remove_id_div = document.getElementById("ingredient_" + id + "_id").style = "display:none;";
        remove_qty_div = document.getElementById("item_qty_" + id).style = "display:none;";
        remove_button = document.getElementById("item_remove_" + id).style = "display:none;";
        remove_hr = document.getElementById("item_hr_" + id).style = "display:none;";
        remove_name_div = document.getElementById("item_name_" + id).style = "display:none;";
        deleted = document.getElementById("deleted_" + id);
        deleted.value = "yes";
        remove_qty_div = document.getElementById("i_item_qty_" + id).value = 0;
    }
</script>

</html>

<!-- Send Data to Modify product -->
<?php
// Check Submit Button Cliecked
if (isset($_POST['submit'])) {
    echo "<pre>";
    echo $count;
    print_r($_POST);
    echo "</pre>";

    for ($i = 0; $count > $i; $i++) {
        $isDeleted = $_POST["deleted_" . $i];
        if ($isDeleted == "no") {
            $this_ing_id = $_POST["id_" . $i];
            $this_item = $_POST["item_" . $i];
            $this_qty = $_POST["qty_" . $i];
            // Update Ingredient
            $sql = "UPDATE `makeProduct` SET `id`='{$this_ing_id}', `item_name` ='{$this_item}',`qty`='{$this_qty}'  WHERE id = {$this_ing_id}";
            insert_query($sql, "Successfully Modified <b>{$product_name}</b> product!<br/>");
        } elseif ($isDeleted == "yes") {
            $this_ing_id = $_POST["id_" . $i];
            $this_item = $_POST["item_" . $i];
            $sql = "DELETE FROM `makeProduct` WHERE id={$this_ing_id}";
            if (mysqli_query($con, $sql)) {
                echo "<br/>id: {$this_ing_id}, {$this_item} deleted successfully<br/>";
            } else {
                echo "Error deleting record: " . mysqli_error($con);
            }
        }
    }

    // Refresh Page
    $this_url = basename($_SERVER["SCRIPT_FILENAME"]);
    // header("refresh:2; url={$this_url}");
    echo "<script>
    // setTimeout(`location.href = '$this_url';`, 3000);
    </script> ";
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