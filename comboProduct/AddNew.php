<?php require_once '../inc/config.php';
require_once '../inc/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Add New Combo Product</title>
</head>

<body>
    <h1>Add New Combo Product</h1>
    <form action="AddNew.php" method="post">
        <div class="container">

            <!-- Product Details Section  -->
            <div class="productDetails">
                <h2>Product Details</h2>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="text" name="price" id="price" class="form-control">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image" class="form-control">
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
                            <th>Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rawItemsBody"></tbody> <!-- Moved tbody here -->
                </table>
                <div style="padding: 5px; margin:10px;">
                    Select Raw Item : <input type="text" list="rawItems" id="rawItem">
                    QTY : <input type="number" id="qty" value="1"> <br>
                    <button type="button" id="addRawItem">Add Raw Item</button>
                    <datalist id="rawItems">
                        <!-- == Employee == -->
                        <?php $rawItems = "SELECT item_name FROM items";
                        $result = mysqli_query($con, $rawItems);
                        if ($result) {
                            echo "<ol>";
                            while ($recoard = mysqli_fetch_assoc($result)) {
                                $product = $recoard['item_name'];
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
                    document.getElementById("addRawItem").addEventListener("click", function() {
                        // Get selected raw item and quantity
                        var rawItem = document.getElementById("rawItem").value;
                        var qty = document.getElementById("qty").value;

                        // Fetch cost and ID of selected raw item from database using AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open("GET", "get_item_details.php?item_name=" + encodeURIComponent(rawItem), true);
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                var response = JSON.parse(xhr.responseText); // Parse the response JSON

                                // Extract cost and ID from the response
                                var price = parseFloat(response.price); // Parse the price to float
                                var itemId = response.id;

                                // Calculate the cost
                                var cost = price * parseFloat(qty);

                                // Append raw item details to table
                                var newRow = "<tr><td>" + rawItem + "</td><td>" + qty + "</td><td>" + cost.toFixed(2) + "</td><td>";
                                newRow += "<button class='deleteRawItem'>Delete</button>";
                                newRow += "<a href='/rawitem.php?id=" + itemId + "'><button class='viewRawItem'>Edit </button></a></td></tr>";
                                document.getElementById("rawItemsBody").innerHTML += newRow;

                                // Add event listener for delete button of the newly added row
                                var deleteButtons = document.getElementsByClassName("deleteRawItem");
                                for (var i = 0; i < deleteButtons.length; i++) {
                                    deleteButtons[i].addEventListener("click", function() {
                                        this.parentNode.parentNode.remove(); // Remove the row
                                    });
                                }
                            }
                        };
                        xhr.send();

                        // Clear the input fields
                        document.getElementById("rawItem").value = "";
                        document.getElementById("qty").value = "1";
                    });
                </script>


            </div>

            <!-- Raw Items Section End -->

            <!-- Machines Section  -->
            <div class="machines">
                <h2>Machines</h2>
                <div class="form-group">
                    <label for="machine">Machine</label>
                    <select name="machine" id="machine" class="form-control">

                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="text" name="quantity" id="quantity" class="form-control">
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary" id="addMachine">Add Machine</button>
                </div>
            </div>
            <!-- Machines Section End -->

        </div>
    </form>
</body>

</html>

<style>
    body {
        background: #000;
        color: white;
    }

    h1 {
        text-align: center;
        margin-top: 30px;
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
</style>


<script>
    document.getElementById('days').addEventListener('change', (event) => {
        const newDays = parseInt(event.target.value);
        const body = document.getElementById('optionalArea');
        const existingDays = body.children.length;

        if (newDays < existingDays) {
            // Remove extra elements
            for (let dayCount = existingDays; dayCount > newDays; dayCount--) {
                body.removeChild(body.lastElementChild);
            }
        } else if (newDays > existingDays) {
            // Add new elements
            for (let dayCount = existingDays + 1; dayCount <= newDays; dayCount++) {
                const dayDiv = document.createElement('div');
                dayDiv.innerHTML = `<div class="row">
                                            <label for="name${dayCount}">Day ${dayCount}</label>
                                            <div class="col-md-6">
                                                <div class="form-inner mb-30">
                                                    <label>Add Name</label>
                                                    <input type="text" placeholder="Name here..." id="name${dayCount}">
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-30">
                                                <div class="form-inner">
                                                    <label>Description</label>
                                                    <textarea placeholder="Description here" id="description${dayCount}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="upload-img-area">
                                            <input type="file" name="image${dayCount}" id="image${dayCount}" accept="image/*">
                                        </div>`;
                body.appendChild(dayDiv);
            }
        }
    });

    function uploadData() {
        const title = document.getElementById('title').value;
        const days = document.getElementById('days').value;
        const desc = document.getElementById('description').value;
        const mainImage = document.getElementById('mainImage').files[0];

        const formData = new FormData();

        formData.append('title', title);
        formData.append('days', days);
        formData.append('desc', desc);
        formData.append('mainImage', mainImage);

        for (let i = 1; i <= days; i++) {
            formData.append('name' + i, document.getElementById('name' + i).value);
            formData.append('description' + i, document.getElementById('description' + i).value);
            formData.append('image' + i, document.getElementById('image' + i).files[0]);
        }

        fetch('addTourProcess.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                console.log(result);
                alert(result);
                if (result === "Tour added successfully") {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    }
</script>