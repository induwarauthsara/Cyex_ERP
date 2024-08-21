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
    <title>Raw Item List</title>
</head>

<body>
    <h1>Raw Item List</h1>
    <div id="add_raw_item">Add New Raw Item</div>

    <table id='DataTable'>
        <thead>
            <tr>
                <th>Raw Item Name</th>
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
            $sql = "SELECT * FROM `items`";
            if ($result = mysqli_query($con, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $itemName = $row['item_name'];
                        $itemCost = $row['cost'];
                        $itemQty = $row['qty'];
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
    // ===================================== Add New Raw Item ===================================== 
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

    // ===================================== Edit Raw Item =====================================

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
                    .then(responseText => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            // text: 'Failed to update item cost!',
                            text: responseText,
                        });
                        return responseText;
                    })
                    .catch(error => {
                        // Handle error response
                        console.error('Error:', error);
                        throw new Error('Failed to update item cost');
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

    // ===================================== Buy Raw Item =====================================
    function buyRawItem(itemId, itemName) {
        Swal.fire({
            title: 'Buy ' + itemName,
            html: '<label for="itemCost">Single Unit Cost:</label>' +
                '<input type="number" oninput="changeTotal()" onchange="changeTotal()" id="itemCost" placeholder="Enter Single Unit Cost"> <br>' +
                ` 
                <label for="packetPurchaseCheckBox">Packet Purchase : </label> <input type="checkbox" id="packetPurchaseCheckBox" onchange="changePacketPurchaseCheckBox()"> <br>
                <div id="packetPurchaseDiv" style="display:none;">
                    <label for="packetQty">Number of Packet Purchased:</label>
                    <input type="number" id="packetQty" placeholder="Enter Number of Packets you Purchased" oninput="changeTotal()" disabled> <br>
                    <label for="quantityPerPacket">Qty per Packet (in Pcs/gm/ml/kg):</label>
                    <input type="number" id="quantityPerPacket" placeholder="Enter Quantity (Pcs/gm) per Packet" oninput="packetPrice();" oninput="packetPrice();" disabled> <br>
                    <label for="packetPrice">One Packet Price (optional) :</label>
                    <input type="number" id="packetPrice" oninput="packetPrice()" onchange="packetPrice()" placeholder="Enter Packet Price" disabled> <br>

                </div>
                 ` +
                '<br> <label for="itemQty">Quantity:</label>' +
                '<input type="number" oninput="changeTotal()" id="itemQty" placeholder="Enter Quantity"> <br>' +
                `Total : <span id="total"></span> <br>
                <label for="creditPaymentCheckBox">Credit Purchase : </label> <input type="checkbox" id="creditPaymentCheckBox" onchange="changeCreditPaymentCheckBox()"> <br>
                <div id="creditPaymentDiv" style="display:none;">
                    <label for="creditPayment">First Payment:</label>
                    <input type="number" id="creditPayment" placeholder="Enter Down Payment" oninput="changeCreditPaymentCheckBox();" onchange="changeCreditPaymentCheckBox()" disabled> <br>
                    <label for="BalancePaymentDate">Balance Payment Date:</label> <input type="date" id="BalancePaymentDate" disabled> <br>
                    Balance Payment: <span id="balancePayment"></span> <br>
                </div>
                <br><label for="paymentAccount">Payment Account:</label> <select id="paymentAccount"> <?php echo implode('', $bankAccountsOptionList); ?> </select> <br>
                <label for="supplier">Supplier:</label> <input list="Suppliers" id="supplier" placeholder="Select Supplier">
                `,

            focusConfirm: false,
            preConfirm: () => {
                // Retrieve the quantity of raw item to buy
                var singleUnitCost = document.getElementById('itemCost').value;
                //packet purchase
                var packetPurchaseCheckBox = document.getElementById('packetPurchaseCheckBox').checked;
                var purchasedPacketQty = document.getElementById('packetQty').value;
                var quantityPerPacket = document.getElementById('quantityPerPacket').value;
                var packetPrice = document.getElementById('packetPrice').value;
                // credit purchase
                var creditPaymentCheckBox = document.getElementById('creditPaymentCheckBox').checked;
                var FirstPayment = document.getElementById('creditPayment').value;
                var BalancePaymentDate = document.getElementById('BalancePaymentDate').value;
                var BalancePayment = document.getElementById('balancePayment').innerHTML;

                var itemQty = document.getElementById('itemQty').value;
                var BillTotal = document.getElementById('total').innerHTML;
                var paymentAccount = document.getElementById('paymentAccount').value;
                var supplier = document.getElementById('supplier').value;

                // singleUnitCost and itemQty are must be a number and it must greater than 0. 
                if (isNaN((parseInt(singleUnitCost))) || parseFloat(singleUnitCost) <= 0) {
                    Swal.showValidationMessage('Please enter a valid single unit cost');
                    return false;
                }
                if (isNaN(parseInt(itemQty)) || parseInt(itemQty) <= 0) {
                    Swal.showValidationMessage('Please enter a valid quantity');
                    return false;
                }
                // Check if credit payment is selected and validate the credit payment fields
                if (creditPaymentCheckBox.checked) {
                    if (isNaN(parseInt(FirstPayment)) || parseFloat(FirstPayment) <= 0) {
                        Swal.showValidationMessage('Please enter a valid down payment');
                        return false;
                    }
                    if (!BalancePaymentDate) {
                        Swal.showValidationMessage('Please select a balance payment date');
                        return false;
                    }
                    if (!BalancePaymentDate) {
                        Swal.showValidationMessage('Please select a balance payment date');
                        return false;
                    }
                }
                // Packet purchase validation
                if (packetPurchaseCheckBox.checked) {
                    if (isNaN(purchasedPacketQty) || parseInt(purchasedPacketQty) <= 0) {
                        Swal.showValidationMessage('Please enter a valid number of packets purchased');
                        return false;
                    }
                    if (isNaN(quantityPerPacket) || parseInt(quantityPerPacket) <= 0) {
                        Swal.showValidationMessage('Please enter a valid quantity per packet');
                        return false;
                    }
                    if (isNaN(packetPrice) || parseFloat(packetPrice) <= 0) {
                        Swal.showValidationMessage('Please enter a valid packet price');
                        return false;
                    }
                }
                // Validate payment account and supplier
                if (!paymentAccount) {
                    Swal.showValidationMessage('Please select a payment account');
                    return false;
                }
                if (!supplier) {
                    Swal.showValidationMessage('Please select a supplier');
                    return false;
                }



                // Send the buy request to the server
                return fetch(`purchaseRawItems-submit.php?itemId=${itemId}&itemName=${itemName}&singleUnitCost=${singleUnitCost}&itemQty=${itemQty}&paymentAccount=${paymentAccount}&supplier=${supplier}&purchasedPacketQty=${purchasedPacketQty}&quantityPerPacket=${quantityPerPacket}&packetPrice=${packetPrice}&FirstPayment=${FirstPayment}&BalancePaymentDate=${BalancePaymentDate}&BalancePayment=${BalancePayment}&BillTotal=${BillTotal}&creditPaymentCheckBox=${creditPaymentCheckBox}&packetPurchaseCheckBox=${packetPurchaseCheckBox}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to buy raw item');
                        }
                        return response.text();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            // text: 'Failed to buy raw item!',
                            text: error,
                        });
                    });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    // text: 'Raw Item purchased successfully!',
                    text: result.value,
                    timer: 2000, // Close alert after 2 seconds
                    // timerProgressBar: true
                });
            }
        });
    }

    // onInput itemPrice & inputQty change total
    function changeTotal() {
        var itemQty = document.getElementById('itemQty').value;
        var itemCost = document.getElementById('itemCost').value;
        var totalSpan = document.getElementById('total');

        // Check itemQty & itemCost are not empty
        // if (!isNaN(parseFloat(itemQty)) || !isNaN(parseFloat(itemCost))) {
        var total = itemQty * itemCost;
        totalSpan.innerHTML = total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // show total in "123,456.78" format
        // }
        changeCreditPaymentCheckBox();
    }

    function packetPrice() {
        var packetPrice = document.getElementById('packetPrice').value;
        var quantityPerPacket = document.getElementById('quantityPerPacket').value;
        var itemCost = document.getElementById('itemCost');

        var singleUnitPrice = packetPrice / quantityPerPacket;
        itemCost.value = singleUnitPrice;
        changeTotal();
    }

    function changePacketPurchaseCheckBox() {
        const packetPurchaseCheckBox = document.getElementById("packetPurchaseCheckBox");
        const packetPurchaseDiv = document.getElementById("packetPurchaseDiv");
        const packetQty = document.getElementById("packetQty");
        const quantityPerPacket = document.getElementById("quantityPerPacket");
        const packetPrice = document.getElementById("packetPrice");
        const itemQty = document.getElementById("itemQty");
        const itemQtyLabel = document.querySelector('label[for="itemQty"]');
        const singleUnitPrice = document.getElementById("itemCost");
        const singleUnitPriceLabel = document.querySelector('label[for="itemCost"]');


        if (packetPurchaseCheckBox.checked) {
            packetPurchaseDiv.style.display = "block";
            packetQty.disabled = false;
            quantityPerPacket.disabled = false;
            packetPrice.disabled = false;
            itemQty.readOnly = true;
            itemQty.placeholder = "Quantity auto-calculated";
            itemQtyLabel.innerText = "Quantity: (auto-calculated)";
            singleUnitPrice.readOnly = true;
            singleUnitPrice.placeholder = "Single Unit Price (auto-calculated)";
            singleUnitPriceLabel.innerHTML = "Single Unit Price: (auto-calculated)";

            // Calculate itemQty based on packetQty and quantityPerPacket
            packetQty.addEventListener('input', updateItemQty);
            quantityPerPacket.addEventListener('input', updateItemQty);
            changeTotal();
        } else {
            packetPurchaseDiv.style.display = "none";
            packetQty.disabled = true;
            quantityPerPacket.disabled = true;
            itemQty.readOnly = false;
            itemQty.placeholder = "Enter Quantity";
            itemQtyLabel.innerText = "Quantity:";
            singleUnitPrice.readOnly = false;
            singleUnitPrice.placeholder = "Single Unit Cost:";
            singleUnitPriceLabel.innerHTML = "Single Unit Cost:";

            // Remove event listeners when checkbox is unchecked
            packetQty.removeEventListener('input', updateItemQty);
            quantityPerPacket.removeEventListener('input', updateItemQty);
        }
        changeTotal();
    }

    function updateItemQty() {
        const packetQty = document.getElementById("packetQty").value;
        const quantityPerPacket = document.getElementById("quantityPerPacket").value;
        const itemQty = document.getElementById("itemQty");

        itemQty.value = packetQty * quantityPerPacket;
        changeTotal();
    }

    function changeCreditPaymentCheckBox() {
        const creditPaymentCheckBox = document.getElementById("creditPaymentCheckBox");
        const creditPaymentDiv = document.getElementById("creditPaymentDiv");
        const creditPayment = document.getElementById("creditPayment");
        const balancePaymentDate = document.getElementById("BalancePaymentDate");
        const balancePayment = document.getElementById("balancePayment");

        // Function to update balance payment
        function updateBalancePayment() {
            const total = document.getElementById("total").innerText.replace(/,/g, ''); // Remove commas from total
            const numberTotal = parseFloat(total) || 0; // Parse total as float
            const creditValue = parseFloat(creditPayment.value) || 0; // Parse creditPayment as float
            const balancePaymentValue = (numberTotal - creditValue).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Format balance payment
            balancePayment.innerText = balancePaymentValue;
        }

        if (creditPaymentCheckBox.checked) {
            creditPaymentDiv.style.display = "block";
            creditPayment.disabled = false;
            balancePaymentDate.disabled = false;

            // Initial update of balance payment
            updateBalancePayment();

            // Attach event listeners only once
            creditPayment.addEventListener('input', updateBalancePayment);
            creditPayment.addEventListener('change', updateBalancePayment);
        } else {
            creditPaymentDiv.style.display = "none";
            creditPayment.disabled = true;
            balancePaymentDate.disabled = true;

            // Reset balance payment when checkbox is unchecked
            balancePayment.innerText = '';
        }
    }

    // ===================================== Delete Raw Item =====================================
    function deleteRawItem(itemId, itemName) {
        Swal.fire({
            title: 'Delete ' + itemName,
            text: "Are you sure you want to delete this raw item?",
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
                            throw new Error('Failed to delete raw item');
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