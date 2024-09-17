<?php require_once '../inc/config.php';
require_once '../inc/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <title>Add New Product</title>
</head>

<body>
    <h1>Add New Product</h1>
    <form method="post" action="AddNewComboProduct-submit.php">
        <div class="container">
            <!-- Product Details Section  -->
            <div class="productDetails">
                <h2>Product Details</h2>
                <div class="form-group">
                    <label for="name">Product Name :</label>
                    <input type="text" name="productName" id="productName" class="form-control" required> <br>
                    <span>Product Name = Brand Name + Product Name + Modal + (Warranty Period)</span> <br>
                </div>
                <div class="form-group"><br>
                    <label for="price">Selling Price :</label>
                    <input type="number" id="productRate" name="productRate" placeholder="Enter Selling Price" class="form-control" oninput="updateTotals()" required>
                </div>
                <div class="form-group">
                    <label for="stockAvailable">Stock Available Count :</label>
                    <input type="number" id="stockAvailable" name="stockAvailable" placeholder="Enter Stock Available Count" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cost">Cost :</label>
                    <input type="number" id="cost" name="cost" placeholder="Enter Cost" class="form-control" oninput="updateTotals()" required>
                </div>
                <div class="form-group">
                    <label for="lowStockAlertLimit">Low Stock Alert Limit :</label>
                    <input type="number" id="lowStockAlertLimit" name="lowStockAlertLimit" placeholder="Enter Low Stock Alert Limit" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="Commission">Worker Commission :</label>
                    <input type="number" name="Commission" id="Commission" class="form-control" value="0.00" oninput="updateTotals()">
                </div>
            </div>
            <!-- Product Details Section End -->
            <script>
                // Function to calculate and update final cost, profit, and display
                function updateTotals() {
                    // Get Product Rate
                    var productRate = parseFloat(document.getElementById("productRate").value) || 0;
                    document.getElementById("productRateDisplay").textContent = productRate.toFixed(2);


                    // Calculate final cost
                    var ProductCost = parseFloat(document.getElementById("cost").value) || 0;
                    var WorkerCommission = parseFloat(document.getElementById("Commission").value) || 0;
                    var TotalCost = ProductCost + WorkerCommission;
                    document.getElementById("finalCost").textContent = TotalCost.toFixed(2);

                    // Calculate profit
                    var profit = productRate - TotalCost;
                    document.getElementById("profit").textContent = profit.toFixed(2);
                }
            </script>

        </div>
        <div id="totals">
            <p>Product Selling Price : &nbsp<span id="productRateDisplay" class="Rate">0.00</span></p>
            <p>Product Total Cost : &nbsp<span id="finalCost" class="Cost">0.00</span></p>
            <p>Product Profit : &nbsp <span id="profit" class="Profit">0.00</span></p>
        </div>
        <input type="submit" id="submitData" />
    </form>
</body>

</html>

<style>
    /* body {
    background: #000;
    color: white;
    } */

    h1 {
        text-align: center;
        margin: 30px;
        font-size: 3rem;
    }

    .container {
        display: flex;
        justify-content: space-evenly;
    }

    h2 {
        text-align: center;
    }

    .form-group {
        margin: 10px;
    }

    .form-control {
        width: 300px;
        padding: 5px;
    }

    #addRawItem,
    #submitData,
    #updateData {
        margin: 10px;
        padding: 10px 80px;
        font-size: 1.3rem;
        background-color: green;
        color: white;
        border: none;
        border-bottom: 2px solid darkgreen;
        align-items: center;
        border-radius: 0.5rem;
        cursor: pointer;
    }

    button[type="submit"] {
        font-size: 1.8rem;
        font-weight: bold;
        padding: 20px 80px;
        margin: 30px;
    }

    /* CSS for loading spinner animation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #totals span {
        font-size: 1.5rem;
        font-weight: bold;
        line-height: 2rem;
    }

    .Rate {
        color: blue;
    }

    .Cost {
        color: red;
    }

    .Profit {
        color: green;
    }

    #totals {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin: 20px auto;

    }

    p {
        font-weight: bold;
    }
</style>