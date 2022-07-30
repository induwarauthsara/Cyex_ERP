<?php require_once '../../inc/config.php'; ?>
<?php require_once '../../inc/header.php';
include '../auth.php' ?>

<?php
// Fie Variables
$tabel_name = "items";

// Get Page Number
if (!isset($_POST['page'])) {
    $_POST['page'] = 1;
    $page = $_POST['page'];
} else {
    $page = $_POST['page'];
}


if (isset($_GET['order'])) {
    $item = $_GET['item'];
    $sort = $_GET['sort'];
    $order = $_GET['order'];
    $display = $_GET['display'];
} else {
    $item = "";
    $sort = "id";
    $order = "ASC";
    $display = "10";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items</title>
    <link rel="stylesheet" href="../dashboard-page.css">
</head>

<body>
    <div class="content-wrapper">
        <?php require_once 'menu.php'; ?>

        <h1>List</h1>
        <fieldset>
            <legend>Search Item</legend>
            <form action="" method="GET" id="search">
                <input name="tab" value="list" style="display:none;" />
                <div class="form_field"> <label for="select_item"> Select Item:</label>
                    <input list="item_list" type="text" name="item" value="<?php echo $item; ?>" <?php if (!isset($_GET['item'])) {
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

                <div class="form_field"> <input type="submit" value="Search Item" id="submit-btn">
                </div>
            </form>
        </fieldset>

        <br> <br>

        <?php
        $all_row_sql = "SELECT * FROM {$tabel_name} WHERE item_name LIKE '%$item%'";
        $all_row_result = mysqli_query($con, $all_row_sql);
        $all_rows = mysqli_num_rows($all_row_result);
        $list_start_from = ($page - 1) * $display;
        // echo $list_start_from;

        $sql = "SELECT * FROM {$tabel_name} WHERE item_name LIKE '%$item%' ORDER BY {$sort} {$order} LIMIT $list_start_from, $display";
        if ($result = mysqli_query($con, $sql)) {
            $rows = mysqli_num_rows($result);
            if ($rows > 0) {
                echo "<table>";
                echo "<tr>";
                echo "<th>id</th>";
                echo "<th>Item Name</th>";
                echo "<th>Description</th>";
                echo "<th>Cost</th>";
                echo "<th>QTY</th>";
                echo "<th>Supplier</th>";
                echo "<th>Edit</th>";
                echo "<th>Buy</th>";
                echo "</tr>";
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['item_name'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>" . Round($row['cost'], 2)  . "</td>";
                    echo "<td>" . Round($row['qty'], 2)  . "</td>";
                    echo "<td>" . $row['supplier'] . "</td>";
                    echo "<td> <a href='modify.php?id= {$row['id']} '>Edit</a> </td>";
                    echo "<td> <a href='buy.php?id= {$row['id']} '>Buy</a> </td>";
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

        <br>
        <!-- Page List -->
        <div id="page_list">
            <!--            <button name="previous_page" onClick="navigatePage('pre');" value="<?php /* echo $page++; ?>">previous </button> >
                <button name="next_page" onClick="navigatePage('nn'); " value="<?php echo $page++; */ ?>"> Next </button> -->

            <form action="" method="POST">
                <?php
                $all_pages = ceil($all_rows / $display);

                for ($i = 1; $i <= $all_pages; $i++) {
                    echo "<button name='page' value='$i'>$i</button>";
                }
                // echo $page;
                ?>
            </form>

        </div>
        <!-- Page List end -->

        <style>
            #page_list {
                width: 300px;
                display: flex;
                margin: 10px;
                justify-content: space-between;
            }

            #page_list button {
                display: inline;
                border: 0;
                border-radius: 0.25rem;
                background: green;
                color: white;
                font-family: system-ui, sans-serif;
                font-size: 1rem;
                line-height: 1.2;
                white-space: nowrap;
                text-decoration: none;
                padding: 0.25rem 0.5rem;
                margin: 0.25rem;
                cursor: pointer;
            }

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

            tr:hover {
                background-color: lawngreen;
            }
        </style>
        <!-- == Colum Name list - Data List get from Database == -->
        <datalist id="column_list">
            <!-- == Items == -->
            <?php $column_list = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$tabel_name}'";
            $result = mysqli_query($con, $column_list);
            if ($result) {
                echo "<ol>";
                while ($recoard = mysqli_fetch_assoc($result)) {
                    $item = $recoard['COLUMN_NAME'];
                    echo "<option value='{$item}'>";
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
    </div>
</body>
<?php include '../../inc/footer.php'; ?>

</html>



<?php end_db_con(); ?>