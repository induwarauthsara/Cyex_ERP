<?php require_once '../../inc/config.php'; ?>
<?php require '../../inc/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard-page.css">
    <title>Products</title>
</head>

<body>


    <div class="content-wrapper">
        <h1><U><a href="index.php">Products</a></U></h1>
        <form action="" id="tab_list">
            <input type="submit" name="tab" value="list">
            <input type="submit" name="tab" value="new">
            <input type="submit" name="tab" value="modify">
        </form>
        <hr>
        <?php
        if (isset($_GET['tab'])) {

            // Set Veriable
            $tab = $_GET['tab'];

            // Item List
            if ($tab == 'list') {
                require_once 'list.php';
            }

            // Add New Item
            if ($tab == 'new') {
                require_once 'new.php';
            }

            // Modify Item
            if ($tab == 'modify') {
                require_once 'modify.php';
            }
        } else {
            require_once 'menu.php';
        }
        ?>

    </div>

</body>

</html>