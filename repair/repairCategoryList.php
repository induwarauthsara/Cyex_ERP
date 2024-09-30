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
    <title>Repair Category List</title>
</head>

<body>
    <h1>Repair Category List</h1>
    <div id="add_new_category">New Repair Category</div>

    <table id='DataTable'>
        <thead>
            <tr>
                <th>id</th>
                <th>Repair Category Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM `repair_categories`";
            if ($result = mysqli_query($con, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        $repair_id = $row['id'];
                        $category_name = $row['category_name'];
                        echo "<td> $repair_id </td>";
                        echo "<td> $category_name </td>";
                        // echo "<td><button onclick='showEditProductModal( `$repair_name`, `$rate`,  `$cost`, `$repair_id`)'> Edit </button></td>";
                        // echo "<td><button class='btn-danger' onclick='deleteProduct(`$repair_id`, `$repair_name`)'>Delete</button></td>";
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


<!-- Add Repair Category -->
<script src="add_repair_category_modal.js"></script>

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
    #updateData,
    #add_new_category {
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


    #add_new_category {
        background: #3edd00;
        color: black;
        margin: 5px;
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1.3rem;
        width: auto;
        cursor: pointer;
        top: 80;
        position: absolute;
        right: 50;
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
    function showEditProductModal(productName, productRate, productCost, productId) {
        Swal.fire({
            title: 'Edit "' + productName + '" Details',
            // html: document.getElementById('editProductModal'),
            html: '<label for="productName">Product Name:</label> ' +
                '<input type="text" id="productName" value="' + productName + '" > <br>' +
                '<label for="productRate">Product Rate:</label>' +
                '<input type="number" id="productRate" value="' + productRate + '"> <br>' +
                '<input type="hidden" id="productCost" value="' + productCost + '"> <br>',
            focusConfirm: false,
            preConfirm: () => {
                // Retrieve updated product rate
                const newProductName = document.getElementById('productName').value;
                const newProductRate = document.getElementById('productRate').value;
                const ProductCost = document.getElementById('productCost').value;
                const newProductProfit = newProductRate - ProductCost;

                // Send the updated data to the server for processing
                return fetch(`update_product-submit.php?productId=${productId}&productRate=${newProductRate}&productProfit=${newProductProfit}&productName=${newProductName}`)
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
                            // text: 'Failed to update product details!',
                            text: error,
                        });
                    });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // alert(result.value);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    // text: 'Product updated successfully!',
                    text: result.value,
                    timer: 2000, // Close alert after 2 seconds
                    // timerProgressBar: true
                });
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