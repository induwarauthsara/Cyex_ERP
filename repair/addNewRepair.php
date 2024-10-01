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
    <title>Add New Repair</title>
</head>

<body>
    <h1>Add New Repair</h1>
    <div id="action_buttons">
        <div id="add_raw_item">New Repair Stock Item</div>
        <div id="add_new_category">New Repair Category</div>
    </div>
    <form method="post">
        <div class="container">
            <!-- Product Details Section  -->
            <div class="productDetails">
                <h2>Repair Details</h2>
                <div class="form-group">
                    <label for="name">Repair Name</label>
                    <input type="text" name="productName" id="productName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="category">Repair Category</label>
                    <select name="category" id="category" class="form-control">
                        <?php
                        $categories = "SELECT id, category_name FROM repair_categories";
                        $result = mysqli_query($con, $categories);
                        if ($result) {
                            while ($record = mysqli_fetch_assoc($result)) {
                                $category_id = $record['id'];
                                $category_name = $record['category_name'];
                                echo "<option value='{$category_id}'>{$category_name}</option>";
                            }
                        } else {
                            echo "<option value='Result 404'>No Categories Found</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Selling Price</label>
                    <input type="number" id="productRate" name="productRate" placeholder="Enter rate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="commission">Worker Commission</label>
                    <input type="number" id="commission" name="commission" placeholder="Enter commission" class="form-control" oninput="updateTotals()" required>
                </div>
            </div>
            <!-- Product Details Section End -->

            <!-- Repair Stock Items Section  -->
            <div class="RawItems">
                <h2>Repair Stock Items</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rawItemsBody"></tbody> <!-- Moved tbody here -->
                </table>
                <div style="padding: 5px; margin:10px; display:flex; flex-direction:column; align-items:center;">
                    <div>
                        Add Saved Item : <input type="text" list="rawItemsDataList" id="rawItem">
                        QTY : <input type="number" id="qty" value="1"> <br>
                    </div>
                    <button type="button" id="addRawItem">Add </button>
                    <datalist id="rawItemsDataList">
                        <!-- == Raw Item Datalist == -->
                        <?php $rawItems = "SELECT stock_item_name FROM repair_stock";
                        $result = mysqli_query($con, $rawItems);
                        if ($result) {
                            echo "<ol>";
                            while ($record = mysqli_fetch_assoc($result)) {
                                $item_name = $record['stock_item_name'];
                                echo "<option value='{$item_name}'>";
                            }
                            echo "</ol>";
                        } else {
                            echo "<option value='Result 404'>";
                        }
                        ?>
                    </datalist>
                </div>

                <script>
                    // Function to calculate and update final cost, profit, and display
                    function updateTotals() {
                        var finalCost = 0;

                        // Get Product Rate
                        var productRate = parseFloat(document.getElementById("productRate").value);
                        document.getElementById("productRateDisplay").textContent = productRate.toFixed(2);

                        var commission = parseFloat(document.getElementById("commission").value);

                        // Calculate final cost
                        // Final cost is the sum of all qty x cost
                        var costElements = document.querySelectorAll("#rawItemsBody td:nth-child(3)");
                        var qtyElements = document.querySelectorAll("#rawItemsBody td:nth-child(2)");
                        for (var i = 0; i < costElements.length; i++) {
                            // Extract cost and quantity from the current row
                            var cost = parseFloat(costElements[i].innerText);
                            var qty = parseFloat(qtyElements[i].innerText);

                            // Calculate the subtotal for the current row
                            var subtotal = cost * qty;

                            // Add the subtotal to the final cost
                            finalCost += subtotal;
                        }
                        document.getElementById("finalCost").textContent = finalCost.toFixed(2);
                        finalCost += commission;

                        // Calculate profit
                        var productRate = parseFloat(document.getElementById("productRate").value);
                        var profit = productRate - finalCost;
                        document.getElementById("profit").textContent = profit.toFixed(2);
                    }

                    // Append New Raw Item to Table
                    // Event listener for add button
                    document.getElementById("addRawItem").addEventListener("click", function() {
                        // Show loading spinner
                        var spinner = document.createElement("div");
                        spinner.className = "spinner";
                        document.getElementById("rawItemsBody").appendChild(spinner);

                        // Get selected raw item and quantity
                        var rawItem = document.getElementById("rawItem").value;
                        var qty = document.getElementById("qty").value;

                        // Fetch cost and ID of selected raw item from database using fetch API
                        fetch("get_item_details.php?item_name=" + encodeURIComponent(rawItem))
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error("Network response was not ok");
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Extract cost and ID from the response
                                var price = parseFloat(data.price);
                                var itemId = data.id;
                                var cost = price;

                                if (isNaN(price) || price <= 0) {
                                    console.error("Invalid price for raw item:", price);
                                    // Hide loading spinner
                                    spinner.remove();
                                    // Trigger #add_raw_item button click
                                    document.getElementById("add_raw_item").click();
                                    return;
                                }

                                // Calculate the cost
                                // var cost = price * parseFloat(qty);

                                // Append raw item details to table
                                var newRow = "<tr><td>" + rawItem + "</td><td>" + qty + "</td><td>" + cost.toFixed(2) + "</td><td>";
                                newRow += "<button class='deleteRawItem'>X</button> &nbsp; ";
                                // newRow += "<a target='_blank' href='editRawitem.php?id=" + itemId + "'>Edit </a></td></tr>";
                                spinner.remove();
                                document.getElementById("rawItemsBody").innerHTML += newRow;

                                // Create the newRow with display style based on checkbox state



                                // Add event listener for delete button of the newly added row
                                var deleteButtons = document.getElementsByClassName("deleteRawItem");
                                for (var i = 0; i < deleteButtons.length; i++) {
                                    deleteButtons[i].addEventListener("click", function() {
                                        this.parentNode.parentNode.remove(); // Remove the row
                                        // Update totals after removing the item
                                        updateTotals();
                                    });
                                }

                                // Clear the input fields
                                document.getElementById("rawItem").value = "";
                                document.getElementById("qty").value = "1";

                                // Update totals after adding the item
                                updateTotals();

                                // Hide loading spinner
                                spinner.remove();
                            })
                            .catch(error => {
                                console.error("Error fetching raw item details:", error);
                                // Hide loading spinner
                                spinner.remove();
                            });
                    });

                    // Event listener for product rate input
                    document.getElementById("productRate").addEventListener("input", function() {
                        // Update profit when product rate changes
                        updateTotals();
                    });
                </script>


            </div>

            <!-- Repair Stock Items Section End -->


        </div>
        <div id="totals">
            <p>Product Rate : &nbsp<span id="productRateDisplay" class="Rate">0.00</span></p>
            <p>Final Cost : &nbsp<span id="finalCost" class="Cost">0.00</span></p>
            <p>Final Profit : &nbsp <span id="profit" class="Profit">0.00</span></p>
        </div>
        <div id="submitData">Submit</div>
    </form>
</body>

<!-- // Add Repair Stock Item -->
<script src="add_repair_stock_item_modal.js"></script>

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

    #action_buttons {
        display: flex;
        justify-content: space-evenly;
        top: 80;
        position: absolute;
        flex-direction: column;
        right: 50;
    }


    #add_raw_item,
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

    #submitData.disabled {
        background-color: gray;
        cursor: not-allowed;
        pointer-events: none;
        opacity: 0.6;
    }
</style>

<script>
    // Event Listener for Checking Repair Name is already exists or not
    $(document).ready(function() {
        // Event listener for repair name input field
        $('#productName').on('blur', function() {
            var repairName = $(this).val();

            // Check if the repair name is not empty
            if (repairName !== "") {
                $.ajax({
                    url: 'checkRepairNameAlreadyexists.php', // PHP file for checking if the repair name exists
                    method: 'POST',
                    data: {
                        repairName: repairName
                    },
                    success: function(response) {
                        if (response === 'exists') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Repair Name Already Exists',
                                text: 'A repair with this name already exists. Please choose a different name.',
                            });
                            $('#submitData').addClass('disabled'); // Add the disabled class to the div
                        } else {
                            $('#submitData').removeClass('disabled'); // Remove the disabled class from the div
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong with the AJAX request!',
                        });
                    }
                });
            }
        });
    });


    // ====================== Submit All Data to Database for Create New Repair ======================
    // Get product name, rate, image, showInLandingPage, total cost, and profit
    document.getElementById("submitData").addEventListener("click", function() {
        var productName = document.getElementById("productName").value;
        var productRate = document.getElementById("productRate").value;
        var category = document.getElementById("category").value; // Get selected category
        var commission = document.getElementById("commission").value; // Get worker commission
        var finalCost = document.getElementById("finalCost").textContent;
        var profit = document.getElementById("profit").textContent;

        // Validate inputs
        if (!productName || !productRate || !category || !commission) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });
            return;
        }
        if (isNaN(productRate) || isNaN(profit) || isNaN(finalCost) || parseFloat(productRate) <= 0 || parseFloat(productRate) >= 100000000000) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter valid numbers!',
            });
            return;
        }

        // Get all table rows
        var rows = document.querySelectorAll("#rawItemsBody tr");

        // Create an array to store raw item data
        var rawData = [];

        // Iterate over each table row
        rows.forEach(function(row) {
            // Get item name, quantity, and price from the row
            var itemName = row.cells[0].innerText;
            var itemQty = row.cells[1].innerText;
            var itemPrice = row.cells[2].innerText;

            // Push the data to the array
            rawData.push({
                itemName: itemName,
                itemQty: itemQty,
                itemPrice: itemPrice
            });
        });

        // Convert the data to JSON format
        var jsonData = JSON.stringify({
            productName: productName,
            productRate: productRate,
            category: category, // Include category
            commission: commission, // Include commission
            finalCost: finalCost,
            profit: profit,
            rawData: rawData
        });

        console.log(jsonData);

        // Send the data to the server using AJAX
        fetch("addNewRepair-submit.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: jsonData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to submit data');
                }
                return response.text();
            })
            .then(responseText => {
                console.log(responseText); // Handle the response from the server
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Data submitted successfully.',
                    showConfirmButton: false,
                    timer: 2000 // Close alert after 2 seconds
                });
                // redirect to /repair/repair-list.php
                setTimeout(() => {
                    window.location.href = "/repair/repair-list.php";
                }, 100);
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error: ' + error,
                });
            });
    });

    // function appendNewCategoryToDatalist(categoryName) {
    //     var option = document.createElement("option");
    //     option.innerHTML = categoryName;
    //     option.selected = true;
    //     document.getElementById("category").appendChild(option);

    // }
</script>

<!-- Add Repair Category -->
<script src="add_repair_category_modal.js"></script>