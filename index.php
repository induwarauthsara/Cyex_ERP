<?php require_once 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ERP_COMPANY_NAME; ?> - POS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<style>
    /* Styles for Modern POS Layout */
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
    }

    .main {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header img {
        height: 60px;
    }

    .content {
        margin-top: 20px;
    }

    .container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .container input {
        padding: 10px;
        font-size: 16px;
    }

    .button {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    #product-input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
    }

    .highlighted {
        background-color: #f9ff00;
        cursor: pointer;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ccc;
        padding: 10px 0;
    }

    .product-details {
        flex: 1;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
        gap: 10px;
        align-items: center;
    }

    .product-details span {
        font-size: 14px;
    }

    .product-details input {
        width: 100%;
        padding: 5px;
        font-size: 14px;
    }

    .remove-button {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
</style>

<body>
    <div class="main">
        <div class="header">
            <a href="/index.php">
                <img src="logo.png" alt="LOGO">
            </a>
            <div class="company-details">
                <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
                <h2><?php echo $ERP_COMPANY_ADDRESS; ?>
                    <br><?php echo $ERP_COMPANY_PHONE; ?>
                </h2>
            </div>
        </div>
        <hr>
        <div class="content">
            <form id="invoice-form">
                <div class="details">
                    <div class="customer-details">
                        <label for="name">Customer Name: </label>
                        <input type="text" name="name" id="name" autocomplete="off" readonly placeholder="Walk-in Customer" onclick="searchCustomer()">
                        <label for="tele">Customer Number: </label>
                        <input type="text" name="tele" id="tele" autocomplete="off" readonly placeholder="0" onclick="searchCustomer()">
                    </div>
                    <div id="customer-suggestions"></div>
                </div>
                <div class="product-container">
                    <input type="text" id="product-input" placeholder="Enter Product Name / SKU / Scan Code" autocomplete="off">
                    <button id="add-product">Add Product</button>
                </div>
                <div class="product-list" id="product-list"></div>
            </form>
        </div>
        <div class="button">
            <button id="pay-btn">Pay</button>
            <button id="credit-btn">Credit</button>
        </div>
    </div>

    <script>
        // Handling Product List and Local Storage
        let productList = JSON.parse(localStorage.getItem('productList')) || [];

        $(document).ready(function() {
            renderProductList();

            // Add Product Event
            $('#add-product').click(function(e) {
                e.preventDefault();
                let product = $('#product-input').val().trim();
                if (product) {
                    addProduct(product);
                }
                $('#product-input').val('');
            });

            // Modify Keyboard Shortcuts for Modal
            $(document).on('keydown', function(event) {
                if (event.key === "Insert") {
                    $('#product-input').focus();
                }
            });

            // Currency formatting input fields
            $(document).on('focus', '.currency-input', function() {
                $(this).val($(this).val().replace(/,/g, ''));
            }).on('blur', '.currency-input', function() {
                $(this).val(parseFloat($(this).val()).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            });
        });

        function addProduct(product) {
            let item = productList.find(p => p.name === product);
            if (item) {
                item.quantity++;
            } else {
                productList.push({
                    name: product,
                    quantity: 1
                });
            }
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
        }

        function renderProductList() {
            $('#product-list').empty();
            productList.forEach((product, index) => {
                $('#product-list').append(`
                    <div class="product-item">
                        <span>${product.name}</span>
                        <span>Quantity: ${product.quantity}</span>
                        <button onclick="removeProduct(${index})">Remove</button>
                    </div>
                `);
            });
        }

        function removeProduct(index) {
            productList.splice(index, 1);
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
        }
    </script>
</body>

<datalist id="customer_list">
    <?php $customer_list = "SELECT customer_name FROM customers";
    $result = mysqli_query($con, $customer_list);
    if ($result) {
        while ($recoard = mysqli_fetch_assoc($result)) {
            $customer = $recoard['customer_name'];
            echo "<option value='{$customer}'>";
        }
    } else {
        echo "<option value='Result 404'>";
    }
    ?>
</datalist>

</html>

<script src="inc/searchCustomer function.js"></script>