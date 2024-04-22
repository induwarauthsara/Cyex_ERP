    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <?php
    // Check session_start
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['employee_name'])) {
        header("Location: /login");
    } ?>
    <script src="https://kit.fontawesome.com/dc35af580f.js" crossorigin="anonymous"></script>

    <!-- favicon -->
    <link rel="shortcut icon" href="../logo.png" type="image/x-icon">

    <!-- add main css file -->
    <link rel="stylesheet" href="/style.css">

    <!-- adding jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.5.1.js "></script>

    <ul id="navbar">
        <li class="dropdown dashboard_icon">
            <a href="/"><i class="fas fa-file-invoice header_icons"></i></a>
            <!-- <div class="dropdown-content">
                <a href="/invoice">Invoice</a>
            </div> -->
        </li>

        <li class="dropdown dashboard_icon"> <a href="/invoice">Invoices</a>
            <div class="dropdown-content">
                <a href="/invoice/payment-pending.php">Payment Pending Invoice</a>
            </div>
        </li>
        <li class="dropdown dashboard_icon"> <a href="/comboProduct">Combo Products</a>
            <div class="dropdown-content">
                <a href="/comboProduct/AddNewComboProduct.php">Add New Combo Product</a>
                <a href="/comboProduct/RawItemList.php">Raw Item List</a>
                <a href="/comboProduct/productsList.php">Products List</a>
                <!-- <a href="/dashboard/item/list.php">Raw Item List</a> -->
                <!-- <a href="/dashboard/product">Products List</a> -->
            </div>
        </li>

        <li class="dashboard_icon"> <a href="/pettycash.php">Pettycash</a> </li>
        <li class="dashboard_icon adminOnly"> <a href="/transactionLog">Transaction Log</a> </li>


        <!-- <li><a href="/message"><i class="fas fa-comments header_icons"></i></a></li> -->
        <li class="dropdown dashboard_icon adminOnly">
            <a href="/dashboard" class="dropbtn"><i class="fas fa-cogs header_icons"></i></i></a>
            <!-- <div class="dropdown-content"> -->
            <!-- <a href="/dashboard/item">Item</a> -->
            <!-- <a href="/dashboard/product">Product</a> -->
            <!-- <a href="/dashboard/makeProduct">Mke Product</a> -->
            <!-- </div> -->
        </li>
        <!-- <li class="dashboard_icon"> <a href="/dashboard/item/list.php">Item</a> </li> -->
        <!-- <li class="dashboard_icon"> <a href="/dashboard/product">Product</a> </li> -->
        <!-- <li class="dashboard_icon"> <a href="/dashboard/makeProduct">Make Product</a> </li> -->

        <li style="float:right" class="dropdown">
            <a href="/profile"><?php echo $_SESSION['employee_name'] ?> </a>
            <div class="dropdown-content">
                <a href="/profile/attendance">Attendance</a>
                <a href="/login/logout.php">Logout</a>
            </div>
        </li>

    </ul>

    <?php
    // Function for Displaying Rupess ## 10000 -> (Rs. 10,000.00)
    function asRS($value)
    {
        if ($value < 0) return "-" . asRS(-$value);
        return 'Rs. ' . number_format($value, 2);
    }
    ?>

    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        #navbar {
            list-style-type: none;
            margin: 0px;
            padding: 0;
            overflow: hidden;
            background-color: #333;
            margin-bottom: 10px;
            top: 0;
            width: 100%;
        }

        #navbar li {
            float: left;
            padding: 3px;
        }

        #navbar li a,
        .dropbtn {
            display: inline-block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        #navbar li a:hover,
        .dropdown:hover .dropbtn {
            background-color: green;
        }

        #navbar li.dropdown {
            display: inline-block;
        }

        #navbar .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        #navbar .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        #navbar .dropdown-content a:hover {
            background-color: lawngreen;
        }

        #navbar .dropdown:hover .dropdown-content {
            display: block;
        }

        .header_icons {
            line-height: 20px;
            font-size: 30px;
            margin: 0;
        }


        /* Hide Dashboard Icon for Employees*/
        <?php
        $employee_role = $_SESSION['employee_role'];

        $employee = "Employee";
        $admin = "Admin";
        if ($employee_role !== $admin) {
            echo "i.fas.fa-cogs.header_icons { display: none; }";
            echo ".adminOnly { display: none; }";
        }
        ?>
    </style>