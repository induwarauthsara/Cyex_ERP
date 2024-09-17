<?php require_once '../inc/config.php';
require_once '../inc/header.php';
include '../inc/DataTable_cdn.php';

// Database blueprint
// items (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`)
// products (`product_id`, `product_name`, `description`, `stock_qty`, `rate`, `cost`, `profit`, `has_stock`, `image`, `show_in_landing_page`)
// makeProduct (`id`, `item_name`, `product_name`, `qty`)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <title>Product List</title>
</head>

<body>
    <h1>Product List</h1>
    <table id='DataTable'>
        <thead>
            <tr>
                <th>id</th>
                <th>Product Name</th>
                <th>Available Qty</th>
                <th>Rate</th>
                <th>Cost</th>
                <th>Profit</th>
                <th>Low Stock Alert</th>
                <th>Worker Commission</th>
                <th>Has Stock</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM `products`";
            if ($result = mysqli_query($con, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        $product_id = $row['product_id'];
                        $product_name = $row['product_name'];
                        $stock_qty = $row['stock_qty'];
                        $rate = $row['rate'];
                        $cost = $row['cost'];
                        $profit = $row['profit'];
                        $has_stock = $row['has_stock'];
                        $worker_commision = $row['worker_commision'];
                        $stock_alert_limit = $row['stock_alert_limit'];
                        if ($has_stock == 1) {
                            $has_stock = "Yes";
                        } else {
                            $has_stock = "No";
                        }
                        echo "<td> $product_id </td>";
                        echo "<td> $product_name </td>";
                        echo "<td> $stock_qty </td>";
                        echo "<td> $rate </td>";
                        echo "<td> $cost </td>";
                        echo "<td> $profit </td>";
                        echo "<td> $stock_alert_limit </td>";
                        echo "<td> $worker_commision </td>";
                        echo "<td> $has_stock </td>";
                        echo "<td><button onclick='showEditProductModal( `$product_name`, `$rate`,  `$cost`, `$product_id`, `$stock_qty`, `$stock_alert_limit`, `$worker_commision`)'> Edit </button></td>";
                        echo "<td><button class='btn-danger' onclick='deleteProduct(`$product_id`, `$product_name`)'>Delete</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "No records matching your query were found.";
                }
            } else {
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
            }
            ?>
        </tbody>
    </table>


</body>

</html>

<style>
    h1 {
        text-align: center;
        margin: 30px;
    }


    .swal2-html-container input {
        padding: 5px;
        margin: 3px;
    }

    button {
        padding: 5px 10px;
        font-size: 1rem;
        background-color: green;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: auto;
        display: flex;
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

    a {
        text-decoration: none;
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

    .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-top: 4px solid #333;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
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
    }

    p {
        font-weight: bold;
    }

    .btn-danger {
        background-color: #dc3545;
    }
</style>


<!-- ==========================  Edit Product Modal Box  ========================== -->

<script>
    // Function to initialize SweetAlert2 modal for editing product
    function showEditProductModal(productName, productRate, productCost, productId, stockAvailable, lowStockAlertLimit, commission = 0) {
        Swal.fire({
            title: 'Edit "' + productName + '" Details',
            html: '<div class="form-group">' +
                '<label for="productName">Product Name :</label>' +
                '<input type="text" name="productName" id="productName" class="form-control" value="' + productName + '" required> <br>' +
                '<span>Product Name = Brand Name + Product Name + Model + (Warranty Period)</span> <br>' +
                '</div>' +
                '<div class="form-group"><br>' +
                '<label for="price">Selling Price :</label>' +
                '<input type="number" id="productRate" name="productRate" class="form-control" value="' + productRate + '" placeholder="Enter Selling Price" oninput="updateTotals()" required> <br>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="stockAvailable">Stock Available Count :</label>' +
                '<input type="number" id="stockAvailable" name="stockAvailable" class="form-control" value="' + stockAvailable + '" placeholder="Enter Stock Available Count" required> <br>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="cost">Cost :</label>' +
                '<input type="number" id="cost" name="cost" class="form-control" value="' + productCost + '" placeholder="Enter Cost" oninput="updateTotals()" required> <br>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="lowStockAlertLimit">Low Stock Alert Limit :</label>' +
                '<input type="number" id="lowStockAlertLimit" name="lowStockAlertLimit" class="form-control" value="' + lowStockAlertLimit + '" placeholder="Enter Low Stock Alert Limit" required> <br>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="Commission">Worker Commission :</label>' +
                '<input type="number" name="Commission" id="Commission" class="form-control" value="' + commission + '" placeholder="Enter Worker Commission" oninput="updateTotals()"> <br>' +
                '</div>',
            focusConfirm: false,
            preConfirm: () => {
                // Retrieve updated product details
                const newProductName = document.getElementById('productName').value;
                const newProductRate = document.getElementById('productRate').value;
                const newProductCost = document.getElementById('cost').value;
                const newStockAvailable = document.getElementById('stockAvailable').value;
                const newLowStockAlertLimit = document.getElementById('lowStockAlertLimit').value;
                const newCommission = document.getElementById('Commission').value || 0; // Default to 0 if not entered

                // Calculate product profit
                const newProductProfit = newProductRate - newProductCost - newCommission;

                // Send the updated data to the server for processing
                return fetch(`update_product-submit.php?productId=${productId}&productRate=${newProductRate}&cost=${newProductCost}&productProfit=${newProductProfit}&productName=${newProductName}&stockAvailable=${newStockAvailable}&lowStockAlertLimit=${newLowStockAlertLimit}&commission=${newCommission}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to update product details');
                        }
                        return response.text();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error,
                        });
                    });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Product updated successfully!',
                    timer: 1000, // Close alert after 2 seconds
                });
                // refresh the page 
                location.reload();
            }
        });
    }

    function deleteProduct(productId, productName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete product "${productName}"`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send the updated data to the server for processing
                fetch(`delete_product-submit.php?productId=${productId}&productName=${productName}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to delete product');
                        }
                        return response.text();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data,
                            timer: 2000, // Close alert after 2 seconds
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error,
                        });
                    });
            }
        });
    }
</script>