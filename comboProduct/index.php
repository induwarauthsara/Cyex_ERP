    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Combo Product</title>

    <?php
    require_once '../inc/config.php';
    require_once '../inc/header.php';
    include '../inc/DataTable_cdn.php';


    echo '<div class="form-group">
         Show Cost and Profit : <input type="checkbox" id="showCostProfit">
      </div>';


    // Fetch products and related items
    // $sql = "SELECT * FROM `makeProduct`, products WHERE makeProduct.product_name = products.product_name ORDER BY products.`product_id` ASC;";
    $sql = "SELECT products.product_name, products.rate AS productRate, products.profit AS productProfit, stock_qty, makeProduct.qty AS comboQty,
                    items.item_name, items.qty, items.cost, products.product_id, items.id AS item_id, products.cost as productCost
        FROM `makeProduct`, products, items 
        WHERE makeProduct.product_name = products.product_name 
            AND items.item_name = makeProduct.item_name 
        ORDER BY products.`product_id` ASC;";
    $result = $con->query($sql);

    // Initialize variables for storing current product name
    $currentProduct = null;

    // Start building HTML table
    echo
    "<table border='1' id='DataTable'>
<thead>
    <tr>
            <th class='THproductName'> Product Name </th>
            <th class='THproductPrice'> Product Price </th>
            <th class='THproductProfit'> Product Profit </th>
            <th class='THproductQty'> Available Quantity </th>
            <th class='THproductCost'> Product cost </th>
            <th class='THitemName' style='border-left: 2px solid black;'> Raw Item Name</th>
            <th class='THitemQty'>Stock Available </th>
            <th>Combo Qty</th>
            <th class='THitemCost'> Raw Item Cost </th>
            <th > Action </th>
    </tr>
    </thead>
    <tbody>";

    while ($row = $result->fetch_assoc()) {
        $productName = $row["product_name"];
        $productPrice = $row["productRate"];
        $productProfit = $row["productProfit"];
        $productQty = $row["stock_qty"];
        $productCost = $row["productCost"];
        $itemName = $row["item_name"];
        $itemQty = $row["qty"];
        $itemCost = $row["cost"];
        $product_id = $row["product_id"];
        $itemId = $row["item_id"];
        $productCost = $row["productCost"];
        $comboQty = $row["comboQty"];
        echo "<tr>
            <td class='TDproductName'>$productName <br> <button onclick='showEditProductModal( `$productName`, `$productPrice`,  `$productCost`, `$product_id`)'> Edit </button></td>
            <td class='TDproductPrice'>$productPrice</td>
            <td class='TDproductProfit'>$productProfit</td>
            <td class='TDproductQty'>$productQty</td>
            <td class='TDproductCost'>$productCost</td>
            <td class='TDitemName' style='border-left: 2px solid black;'>$itemName <br>  <button onclick='showEditRawItemModal(`$itemName`, `$itemCost`, `$itemQty`,`$itemId`)'> Edit </button></td>
            <td class='TDitemQty'>$itemQty</td>
            <td >$comboQty</td>
            <td class='TDitemCost'>$itemCost</td>
            <td> <a href='update_comboProduct.php?p=$productName'><button> Edit Combo Product </button> </a></td>
        </tr>";
    }
    echo "</tbody></table>";

    ?>

    <script>
        // ======================  Show and Hide Cost / Profit  ======================
        // Event listener for checkbox
        window.onload = function() {
            var costProfitCells = document.querySelectorAll("tbody .TDproductProfit, tbody .TDproductCost, tbody .TDitemCost, thead .THproductProfit, thead .THproductCost, thead .THitemCost");

            costProfitCells.forEach(function(cell) {
                cell.style.display = "none";
            });

            document.getElementById("showCostProfit").addEventListener("change", function() {
                var costProfitCells = document.querySelectorAll("tbody .TDproductProfit, tbody .TDproductCost, tbody .TDitemCost, thead .THproductProfit, thead .THproductCost, thead .THitemCost");


                if (this.checked) {
                    // If checkbox is checked, show totals section and cost/profit cells
                    costProfitCells.forEach(function(cell) {
                        cell.style.display = "";
                    });
                } else {
                    // If checkbox is not checked, hide totals section and cost/profit cells
                    costProfitCells.forEach(function(cell) {
                        cell.style.display = "none";
                    });
                }
            });
        }
    </script>

    <!-- ==========================  Edit Product Modal Box  ========================== -->

    <script>
        // Function to initialize SweetAlert2 modal for editing product
        function showEditProductModal(productName, productRate, productCost, productId) {
            Swal.fire({
                title: 'Edit Product Details',
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

        // ==========================  Edit Raw Item Modal Box  ==========================


        // Function to initialize SweetAlert2 modal for editing raw item
        function showEditRawItemModal(itemName, itemCost, itemQty, itemId) {
            Swal.fire({
                title: 'Edit Raw Item Details',
                html: ' <label for="itemName">Item Name:</label>' +
                    '<input type="text" id="itemName" value="' + itemName + '"> <br>' +
                    '<label for="itemCost">Item Cost:</label>' +
                    '<input type="number" id="itemCost" value="' + itemCost + '"> <br>' +
                    '<label for="itemQty">Available Quantity:</label>' +
                    '<input type="number" id="itemQty" value="' + itemQty + '"> <br>',
                focusConfirm: false,
                preConfirm: () => {
                    // Retrieve updated item cost
                    const newItemName = document.getElementById('itemName').value;
                    const newItemCost = document.getElementById('itemCost').value;
                    const newItemQty = document.getElementById('itemQty').value;

                    // Send the updated data to the server for processing
                    return fetch(`update_raw_item-submit.php?itemId=${itemId}&itemName=${newItemName}&itemCost=${newItemCost}&itemQty=${newItemQty}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to update item cost');
                            }
                            return response.text();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                // text: 'Failed to update item cost!',
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
                        // text: 'Raw Item Details updated successfully!',
                        text: result.value,
                        timer: 2000, // Close alert after 2 seconds
                        // timerProgressBar: true
                    });
                }
            });
        }
    </script>

    <style>
        button {
            margin: 2px;
            align-items: center;
        }

        .swal2-html-container input {
            padding: 5px;
            margin: 3px;
        }

        a {
            text-decoration: none;
        }
    </style>