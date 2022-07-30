<?php require_once '../../inc/config.php'; ?>
<?php require '../../inc/header.php';
include '../auth.php' ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard-page.css">
    <title>Items</title>
</head>

<body>


    <div class="content-wrapper">
        <!--         <form action="" id="tab_list">
            <input type="submit" name="tab" value="list">
            <input type="submit" name="tab" value="new">
            <input type="submit" name="tab" value="modify">
            <input type="submit" name="tab" value="buy">
        </form> -->
        <?php include 'menu.php' ?>
        <?php
        /*
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

            // Buy Item
            if ($tab == 'buy') {
                require_once 'buy.php';
            }
        } else {
            require_once 'list.php';
        }*/
        header("Location:list.php");

        ?>

    </div>

</body>

</html>