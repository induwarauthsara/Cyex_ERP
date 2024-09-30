<?php require_once '../inc/config.php';
require_once '../inc/header.php';
include '../inc/DataTable_cdn.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <title>Repair Stock Item List</title>
</head>

<body>
    <h1>Repair Stock Item List</h1>
    <div id="add_raw_item">Add New Repair Stock Item</div>

    <table id='DataTable'>
        <thead>
            <tr>
                <th>Item Name</th>
                <th> Cost</th>
                <th>Available Qty</th>
                <th> Cost x Available Qty</th>
                <th>Edit</th>
                <th>Buy</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM `repair_stock`";
            if ($result = mysqli_query($con, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $itemName = $row['stock_item_name'];
                        $itemCost = $row['stock_cost'];
                        $itemQty = $row['stock_qty'];
                        $itemId = $row['id'];
                        $costXQty = $itemCost * $itemQty;
                        $costXQty = number_format($costXQty, 2);
                        echo "<tr>";
                        echo "<td>$itemName</td>";
                        echo "<td>$itemCost</td>";
                        echo "<td>$itemQty</td>";
                        echo "<td>$costXQty</td>";
                        echo "<td><button onclick='showEditRawItemModal(`$itemName`, `$itemCost`, `$itemQty`,`$itemId`)' value='" . $row['id'] . "'>Edit</button></td>";
                        echo "<td><button onclick='buyRawItem(`$itemId`, `$itemName`)' value='" . $row['id'] . "'>Buy</button></td>";
                        echo "<td><button class='btn-danger' onclick='deleteRawItem(`$itemId`, `$itemName`)' value='" . $row['id'] . "'>Delete</button></td>";
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

    .form-control {
        width: 300px;
        padding: 5px;
    }

    #addRawItem,
    #submitData,
    #updateData,
    #add_raw_item {
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


    #add_raw_item {
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

<script>
    // ===================================== Edit Raw Item =====================================

    // Function to initialize SweetAlert2 modal for editing raw item
    function showEditRawItemModal(itemName, itemCost, itemQty, itemId) {
        Swal.fire({
            title: 'Edit Repair Stock Item Details',
            html: `
            <label for="itemName">Item Name:</label>
            <input type="text" id="itemName" value="${itemName}" class="swal2-input"><br>
            <label for="itemCost">Item Cost:</label>
            <input type="number" id="itemCost" value="${itemCost}" class="swal2-input"><br>
            <label for="itemQty">Available Quantity:</label>
            <input type="number" id="itemQty" value="${itemQty}" class="swal2-input"><br>
        `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Update',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const newItemName = document.getElementById('itemName').value;
                const newItemCost = document.getElementById('itemCost').value;
                const newItemQty = document.getElementById('itemQty').value;

                if (!newItemName || !newItemCost || !newItemQty) {
                    Swal.showValidationMessage('Please fill in all fields');
                    return false;
                }

                return fetch(`update_repair_stock_item-submit.php?itemId=${itemId}&itemName=${newItemName}&itemCost=${newItemCost}&itemQty=${newItemQty}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to update item cost');
                        }
                        return response.text();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.value,
                    timer: 2000,
                });
                // Refresh the page 
                location.reload();
            }
        });
    }


    // ===================================== Buy Raw Item =====================================
    function buyRawItem(itemId, itemName) {
        Swal.fire({
            title: 'Buy ' + itemName,
            html: `
            <label for="itemQty">Quantity:</label>
            <input type="number" id="itemQty" placeholder="Enter Quantity" class="swal2-input"><br>
            <label for="itemCost">Cost:</label>
            <input type="number" id="itemCost" placeholder="Enter Cost" class="swal2-input"><br>
        `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Buy',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const itemQty = document.getElementById('itemQty').value;
                const itemCost = document.getElementById('itemCost').value;

                if (!itemQty || !itemCost) {
                    Swal.showValidationMessage('Please enter both quantity and cost');
                    return false;
                }

                return fetch(`purchaseRepairStockItems-submit.php?itemId=${itemId}&itemQty=${itemQty}&itemCost=${itemCost}&itemName=${itemName}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to buy item');
                        }
                        return response.text();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.value,
                    timer: 2000,
                });
            }
        });
    }


    // ===================================== Delete Raw Item =====================================
    function deleteRawItem(itemId, itemName) {
        Swal.fire({
            title: 'Delete ' + itemName,
            text: "Are you sure you want to delete this item?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send the delete request to the server
                fetch(`delete_raw_item-submit.php?itemId=${itemId}&itemName=${itemName}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to delete item');
                        }
                        return response.text();
                        console.log(response);
                    })
                    .then(responseText => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            // text: 'Raw Item deleted successfully!',
                            text: responseText,
                            timer: 2000, // Close alert after 2 seconds
                            // timerProgressBar: true
                        });
                        // refresh the page
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            // text: 'Failed to delete raw item!',
                            text: error,
                        });
                    });
            }
        });
    }
</script>

<!-- // Add Repair Stock Item -->
<script src="add_repair_stock_item_modal.js"></script>