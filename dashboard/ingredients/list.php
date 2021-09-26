<?php require_once '../../inc/config.php'; ?>

<?php
// Fie Variables
$tabel_name = "ingredients";

if (isset($_GET['order'])) {
    $select = $_GET['select'];

    $search = $_GET['search'];
    $sort = $_GET['sort'];
    $order = $_GET['order'];
    $display = $_GET['display'];
} else {
    $select = "product";
    $search = "";
    $sort = "id";
    $order = "ASC";
    $display = "10";
}
if ($select == "item") {
    $selected = "item_name";
    $selected_list = "item_list";
} else {
    $selected = "product_name";
    $selected_list = "product_list";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingredients</title>
</head>

<body>
    <h1><u>Ingredients List</u></h1>
    <fieldset>
        <legend>Search product</legend>
        <form action="" method="GET" id="search">
            <input name="tab" value="list" style="display:none;" />
            <div class="form_field"> <label for="select_product"> Select <select name="select">
                        <option value='product' <?php if ($select == "product") {
                                                    echo "selected";
                                                } ?>>Product</option>
                        <option value='item' <?php if ($select == "item") {
                                                    echo "selected";
                                                } ?>>Item</option>
                    </select> :</label>
                <input list="<?php echo $selected_list ?>" type="text" name="search" value="<?php echo $search; ?>" <?php if (!isset($_GET['search'])) {
                                                                                                                        echo "autofocus";
                                                                                                                    } ?> />
            </div class="form_field">

            <div>
                <label for="sort"> Sort :</label>
                <!-- == Colums name list - Data List get from Database == -->
                <select name="sort">
                    <!-- == Columns == -->
                    <?php $column_list = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$tabel_name}'";
                    $result = mysqli_query($con, $column_list);
                    if ($result) {
                        while ($recoard = mysqli_fetch_assoc($result)) {
                            $column = $recoard['COLUMN_NAME'];
                            echo "<option value='{$column}'";
                            // Check selected column
                            if ($column == $sort) {
                                echo "selected";
                            }
                            echo "> {$column} </option>";
                        }
                    } else {
                        echo "<option value='Result 404'>";
                    }
                    ?>
                </select>

                <label for="order">by:</label>
                <select name="order">
                    <option value="ASC" <?php if ($order == "ASC") {
                                            echo "selected";
                                        } ?>>Ascending </option>
                    <option value="DESC" <?php if ($order == "DESC") {
                                                echo "selected";
                                            } ?>>Descending </option>
                </select>
            </div>

            <div class="form_field"> <label for="display"> Display:</label>
                <input type="number" name="display" value="<?php echo $display; ?>" />
            </div>

            <div class="form_field"> <input type="submit" value="Search product" id="submit-btn">
            </div>
        </form>
    </fieldset>

    <br> <br>

    <?php
    $sql = "SELECT * FROM {$tabel_name} WHERE {$selected} LIKE '%$search%' ORDER BY {$sort} {$order} LIMIT $display";

    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>id</th>";
            echo "<th>Product </th>";
            echo "<th>Item</th>";
            echo "<th>Qty</th>";
            echo "<th>Modify</th>";
            echo "</tr>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['product_name'] . "</td>";
                echo "<td>" . $row['item_name'] . "</td>";
                echo "<td>" . $row['qty'] . "</td>";
                echo "<td> <a href='?tab=modify&id= {$row['id']} ' target='_blanck'>Edit</a> </td>";
                echo "</tr>";
            }
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
        } else {
            echo "No records matching your query were found.";
        }
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }

    ?>

    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 90%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>

    <!-- == Colum Name list - Data List get from Database == -->
    <datalist id="column_list">
        <!-- == products == -->
        <?php $column_list = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$tabel_name}'";
        $result = mysqli_query($con, $column_list);
        if ($result) {
            echo "<ol>";
            while ($recoard = mysqli_fetch_assoc($result)) {
                $product = $recoard['COLUMN_NAME'];
                echo "<option value='{$product}'>";
            }
            echo "</ol>";
        } else {
            echo "<option value='Result 404'>";
        }
        ?>
    </datalist>

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
</body>

</html>



<?php end_db_con(); ?>