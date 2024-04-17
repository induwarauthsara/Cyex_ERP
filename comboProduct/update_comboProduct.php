<?php require_once '../inc/config.php';
require_once '../inc/header.php';

// Database blueprint
// items (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`)
// products (`product_id`, `product_name`, `description`, `stock_qty`, `rate`, `cost`, `profit`, `has_stock`, `image`, `show_in_landing_page`)
// makeProduct (`id`, `item_name`, `product_name`, `qty`)


$product_name = $_GET['p'];
$comboProduct = "SELECT * FROM products WHERE product_name = '$product_name'";
$result = mysqli_query($con, $comboProduct);
if ($result) {
    $record = mysqli_fetch_assoc($result);
    $product_id = $record['product_id'];
    $product_name = $record['product_name'];
    $product_rate = $record['rate'];
    // $show_in_landing_page = $record['show_in_landing_page'];
    $show_in_landing_page = $record['show_in_landing_page'] == 1 ? true : false;
} else {
    echo "Error fetching combo product details";
}

$rawItems = "SELECT items.item_name, makeProduct.qty, items.cost, items.id 
            FROM makeProduct, items 
            WHERE product_name = '$product_name' AND makeProduct.item_name = items.item_name";
$result = mysqli_query($con, $rawItems);
if ($result) {
    $rawItemsData = [];
    while ($record = mysqli_fetch_assoc($result)) {
        $rawItemsData[] = $record;
    }
} else {
    echo "Error fetching raw items";
}


// include 'AddNewComboProduct.php';
?>

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
    <title>Update Combo Product</title>
</head>

<body>
    <h1>Update Combo Product</h1>
    <div id="add_raw_item">Add New Raw Item</div>
    <form method="post">
        <div class="container">
            <!-- Product Details Section  -->
            <div class="productDetails">
                <h2>Product Details</h2>
                <div class="form-group">
                    <input type="text" name="producId" id="productId" value="<?php echo $product_id ?>" class="form-control" style='display:none;'>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="productName" id="productName" class="form-control">
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="productRate" name="productRate" placeholder="Enter rate" class="form-control">
                    <!-- <input type="text" id="price"> -->
                </div>
                <!-- <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div> -->
                <div class="form-group">
                    Show in Landing Page : <input type="checkbox" name="showInLandingPage" id="showInLandingPage">
                </div>
                <div class="form-group">
                    Delete this Combo Product : <input type="checkbox" name="deleteProduct" id="deleteProduct">
                </div>
                <div class="form-group">
                    Show Cost and Profit : <input type="checkbox" id="showCostProfit">
                </div>

            </div>
            <!-- Product Details Section End -->

            <!-- Raw Items Section  -->
            <div class="RawItems">
                <h2>Raw Items</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Raw Item Name</th>
                            <th>Quantity</th>
                            <th style="display: none;">Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rawItemsBody"></tbody> <!-- Moved tbody here -->
                </table>
                <div style="padding: 5px; margin:10px; display:flex; flex-direction:column; align-items:center;">
                    <div>
                        Select Raw Item : <input type="text" list="rawItemsDataList" id="rawItem">
                        QTY : <input type="number" id="qty" value="1"> <br>
                    </div>
                    <button type="button" id="addRawItem">Add </button>
                    <datalist id="rawItemsDataList">
                        <!-- == Raw Item Datalist == -->
                        <?php $rawItems = "SELECT item_name FROM items";
                        $result = mysqli_query($con, $rawItems);
                        if ($result) {
                            echo "<ol>";
                            while ($record = mysqli_fetch_assoc($result)) {
                                $product = $record['item_name'];
                                echo "<option value='{$product}'>";
                            }
                            echo "</ol>";
                        } else {
                            echo "<option value='Result 404'>";
                        }
                        ?>
                    </datalist>
                </div>

                <script>
                    // Check if the checkbox is checked
                    var showCostProfit = document.getElementById("showCostProfit").checked;

                    // Function to calculate and update final cost, profit, and display
                    function updateTotals() {
                        var finalCost = 0;

                        // Get Product Rate
                        var productRate = parseFloat(document.getElementById("productRate").value);
                        document.getElementById("productRateDisplay").textContent = productRate.toFixed(2);


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

                        // Calculate profit
                        var productRate = parseFloat(document.getElementById("productRate").value);
                        var profit = productRate - finalCost;
                        document.getElementById("profit").textContent = profit.toFixed(2);
                    }

                    // Append New Raw Item to Table
                    // Event listener for add button
                    document.getElementById("addRawItem").addEventListener("click", function() {

                        // Check if the checkbox is checked
                        var showCostProfit = document.getElementById("showCostProfit").checked;

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


                                // Calculate the cost
                                // var cost = price * parseFloat(qty);

                                // Append raw item details to table
                                var displayStyle = showCostProfit ? "" : "none";
                                var newRow = "<tr><td>" + rawItem + "</td><td>" + qty + "</td><td style='display: " + displayStyle + "'>" + cost.toFixed(2) + "</td><td>";
                                newRow += "<button class='deleteRawItem'>Remove</button> &nbsp; ";
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

                    // Show and Hide Cost / Profit
                    // Event listener for checkbox
                    document.getElementById("showCostProfit").addEventListener("change", function() {
                        var totalsSection = document.getElementById("totals");
                        var costProfitCells = document.querySelectorAll("#rawItemsBody td:nth-child(3), thead th:nth-child(3)");

                        if (this.checked) {
                            // If checkbox is checked, show totals section and cost/profit cells
                            totalsSection.style.display = "flex";
                            costProfitCells.forEach(function(cell) {
                                cell.style.display = "";
                            });
                        } else {
                            // If checkbox is not checked, hide totals section and cost/profit cells
                            totalsSection.style.display = "none";
                            costProfitCells.forEach(function(cell) {
                                cell.style.display = "none";
                            });
                        }
                    });


                    // Add New Raw Item
                    document.querySelector('#add_raw_item').addEventListener('click', function() {
                        Swal.fire({
                            title: 'Add Raw Item',
                            html: '<label for="itemName" class="swal2-label">Item Name:</label>' +
                                '<input id="itemName" class="swal2-input" placeholder="Enter Item Name">' +
                                '<label for="itemPrice" class="swal2-label">Item Price:</label>' +
                                '<input id="itemPrice" class="swal2-input" placeholder="Enter Item Price">' +
                                '<label for="itemQty" class="swal2-label">Quantity:</label>' +
                                '<input id="itemQty" class="swal2-input" placeholder="Enter Quantity">',
                            focusConfirm: false,
                            preConfirm: () => {
                                const itemName = Swal.getPopup().querySelector('#itemName').value;
                                const itemPrice = Swal.getPopup().querySelector('#itemPrice').value;
                                const itemQty = Swal.getPopup().querySelector('#itemQty').value;

                                // Validate inputs
                                if (!itemName || !itemPrice || !itemQty) {
                                    Swal.showValidationMessage('Please fill in all fields');
                                    return false;
                                }
                                if (isNaN(itemPrice) || parseFloat(itemPrice) <= 0 || parseFloat(itemPrice) >= 100000000000) {
                                    Swal.showValidationMessage('Please enter a valid item price');
                                    return false;
                                }
                                if (isNaN(itemQty) || parseInt(itemQty) <= 0 || parseInt(itemQty) >= 10000000000) {
                                    Swal.showValidationMessage('Please enter a valid quantity');
                                    return false;
                                }

                                // If all inputs are valid, proceed with adding the raw item
                                return fetch("/inc/add_raw_item.php?itemName=" + encodeURIComponent(itemName) + "&itemPrice=" + itemPrice + "&itemQty=" + itemQty, {
                                        method: 'GET',
                                    })
                                    .then(response => response.text())
                                    .then(html => {

                                        // Append the new raw item dataList
                                        var rawItemsDataList = document.getElementById("rawItemsDataList");
                                        var option = document.createElement("option");
                                        option.value = itemName;
                                        rawItemsDataList.appendChild(option);


                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: "Successfully added raw item: " + itemName + " with price " + itemPrice + " and quantity " + itemQty,
                                            showConfirmButton: false,
                                            timer: 2000 // Close alert after 2 seconds
                                        });
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Something went wrong!',
                                        });
                                    });
                            }
                        });
                    });
                </script>


            </div>

            <!-- Raw Items Section End -->


        </div>
        <div id="totals" style="display:none;">
            <p>Product Rate : &nbsp<span id="productRateDisplay" class="Rate">0.00</span></p>
            <p>Final Cost : &nbsp<span id="finalCost" class="Cost">0.00</span></p>
            <p>Final Profit : &nbsp <span id="profit" class="Profit">0.00</span></p>
        </div>
        <div id="submitData">Submit</div>
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
        top: 100;
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
</style>


<!-- =============================== Load Data from Database to input Fields ==============================-->
<script>
    window.onload = function() {

        // Load data from database to input fields
        document.getElementById("productName").value = "<?php echo $product_name; ?>";
        document.getElementById("productRate").value = "<?php echo $product_rate; ?>";
        document.getElementById("showInLandingPage").checked = <?php echo $show_in_landing_page ? "true" : "false"; ?>;

        // var newRow = "<tr><td>" + rawItem + "</td><td>" + qty + "</td><td style='display: " + displayStyle + "'>" + cost.toFixed(2) + "</td><td>";

        // Load raw items data to table
        var displayStyle = showCostProfit ? "" : "none";
        var rawItemsData = <?php echo json_encode($rawItemsData); ?>;
        var rawItemsBody = document.getElementById("rawItemsBody");
        rawItemsData.forEach(function(item) {
            var newRow = "<tr><td>" + item.item_name + "</td><td>" + item.qty + "</td><td style='display: " + displayStyle + "'>" + item.cost + "</td><td>";
            newRow += "<button class='deleteRawItem'>Remove</button> &nbsp; ";
            // newRow += "<a target='_blank' href='editRawitem.php?id=" + item.id + "'>Edit </a></td></tr>";
            rawItemsBody.innerHTML += newRow;
        });

        // Add event listener for delete button of the existing rows
        var deleteButtons = document.getElementsByClassName("deleteRawItem");
        for (var i = 0; i < deleteButtons.length; i++) {
            deleteButtons[i].addEventListener("click", function() {
                this.parentNode.parentNode.remove(); // Remove the row
                // Update totals after removing the item
                updateTotals();
            });
        }

        // Update totals after loading the data
        updateTotals();
    }
</script>
<!-- =============================== End Load Data from Database to input Fields ==============================-->

<script>
    document.getElementById("submitData").addEventListener("click", function() {
        // Get the product Id
        var productId = document.getElementById("productId").value;

        // Get the product name
        var productName = document.getElementById("productName").value;

        // Get the product rate
        var productRate = document.getElementById("productRate").value;

        // Get the show in landing page value
        var showInLandingPage = document.getElementById("showInLandingPage").checked ? 1 : 0;

        // Get Delete Product value
        var deleteProduct = document.getElementById("deleteProduct").checked ? 1 : 0;

        var finalCost = document.getElementById("finalCost").textContent;
        var profit = document.getElementById("profit").textContent;

        // Validate inputs
        if (!productName || !productRate) {
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
                text: 'Please enter a valid numbers!',
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
            productId: productId,
            productName: productName,
            productRate: productRate,
            // image: image,
            showInLandingPage: showInLandingPage,
            deleteProduct: deleteProduct,
            finalCost: finalCost,
            profit: profit,
            rawData: rawData
        });

        console.log(jsonData);

        // Send the data to the server using AJAX
        fetch("update_comboProduct-submit.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: jsonData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to submit data');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
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
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error:' + error,
                });
            });
    });
</script>