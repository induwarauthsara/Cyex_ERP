<?php
// Only Admin can access this page
session_start();
$employee_role = $_SESSION['employee_role'];
if ($employee_role !== $admin) {
    header("Location: /index.php");
}
require_once '../inc/config.php'; ?>
<?php require '../inc/header.php';
include '../dashboard/auth.php'
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard-page.css">
    <!-- header eke thiyena nisa  <script src="https://kit.fontawesome.com/dc35af580f.js" crossorigin="anonymous"></script> -->
</head>
<?php
$sql = "SELECT sum(balance) from invoice where balance >= 0;";
$payment_pending_total = asRS(mysqli_fetch_assoc(mysqli_query($con, $sql))['sum(balance)']);
?>

<body>
    <div class="content-wrapper">
        <center>
            Total of Payment Pending : <?php echo $payment_pending_total; ?>
            <a href="/invoice/payment-pending.php"> <button>
                    <h3> Check them >></h1>
                </button></a>
        </center>

        <div class="dash_section">
            <h1 class="dash_head">Geranal</h1> <br>
            <div class="dash_list">
                <div class="dash_item">
                    <b>Stock </b><br> Rs.
                    <?php
                    // Get each Item Full Cost (Cost x qty)
                    $item_capital = array();
                    $sql = "SELECT * FROM items";
                    $result = mysqli_query($con, $sql);
                    if ($result) {
                        // qury success
                        if (mysqli_num_rows($result) > 0) {
                            while ($item_capital_sql = mysqli_fetch_array($result)) {
                                $item_cost = $item_capital_sql['cost'];
                                $item_qty = $item_capital_sql['qty'];
                                $item_fullcost = $item_cost * $item_qty;
                                // echo $item_fullcost;
                                array_push($item_capital, $item_fullcost);
                            }
                        } else {
                            echo "No any Item";
                        }
                    } else {
                        echo "Database Query Failed";
                    }

                    $capital_currency = array_sum($item_capital);
                    echo number_format($capital_currency, 2);  ?>
                </div>

                <div class="dash_item">
                    <b>Cash in Hand</b><br>
                    <rs>
                        <?php
                        $sql = "SELECT amount FROM accounts WHERE account_name = 'cash_in_hand'";
                        $result = mysqli_query($con, $sql);
                        if ($result) {
                            $cash_in_hand_amount = mysqli_fetch_array($result);
                            echo $cash_in_hand_amount['amount'];
                        }
                        ?>
                    </rs>
                </div>
            </div><br>


            <!--      <div class="dash_item">
                    <b>Stock Account</b><br>
                    <rs>5662.44 </rs>
                </div>
            </div><br>
            <div class="dash_list">
                <div class="dash_item">
                    <b>Company Profit</b><br>
                    <rs>5662.44 </rs>
                </div>
                <div class="dash_item">
                    <b>Utility Bills</b><br>
                    <rs>5662.44 </rs>
                </div>
            </div>
            <br>
            <div class="dash_list">
                <div class="dash_item">
                    <b>Machines Account</b><br>
                    <rs>55662.44 </rs>
                </div>

            </div> -->
        </div>

        <div class="dash_section">
            <h1 class="dash_head">Stock</h1>
            <div class="dash_list">
                <div class="dash_item">
                    <a href="item">
                        <i class="fa fa-cube" aria-hidden="true"></i>
                        Items</a>
                </div>
                <div class="dash_item">
                    <a href="product">
                        <i class="fas fa-cubes"></i>
                        Products</a>
                </div>
                <div class="dash_item">
                    <a href="makeProduct">
                        <i class="fa fa-sitemap"></i>
                        Make Product</a>
                </div>
            </div>
        </div>

        <div class="dash_section">
            <h1 class="dash_head">Accounts</h1>
            <div class="dash_list">
                <div class="dash_item">
                    <a href="accounts">
                        <i class="fas fa-balance-scale"></i> Shop </a>
                </div>
                <div class="dash_item">
                    <a href="product">
                        <i class="fas fa-hand-holding-usd"></i>
                        Employees
                    </a>
                </div>
            </div>
        </div>

        <div class="dash_section">
            <h1 class="dash_head">Peoples</h1>
            <div class="dash_list">
                <div class="dash_item">
                    <a href="item">
                        <i class="fas fa-user"></i> Employees </a>
                </div>
                <div class="dash_item">
                    <a href="product">
                        <i class="fas fa-truck-moving"></i> Suppliers
                    </a>
                </div>
                <div class="dash_item">
                    <a href="product">
                        <i class="fas fa-users"></i> Customers
                    </a>
                </div>
            </div>
        </div>

        <div class="dash_section">
            <h1 class="dash_head">Task</h1>
            <div class="dash_list">
                <div class="dash_item">
                    <a href="item">
                        <i class="fas fa-tasks"></i> Todo </a>
                </div>
                <div class="dash_item">
                    <a href="product">
                        <i class="fas fa-file-invoice-dollar"></i> Pay Bill
                    </a>
                </div>

            </div>
        </div>


        <div class="dash_section">
            <h1 class="dash_head">Logs</h1>
            <div class="dash_list">
                <div class="dash_item">
                    <a href="product">
                        <i class="fas fa-money-bill-alt"></i> Petty Cash
                    </a>
                </div>
            </div>
        </div>

        <div class="dash_section">
            <h1 class="dash_head">Messages</h1>
            <div class="dash_list">
                <div class="dash_item">
                    <a href="/message">
                        <i class="fas fa-comments"></i> Messages
                    </a>
                </div>
            </div>
        </div>

        <div class="dash_section">
            <h1 class="dash_head">Cron Jobs</h1>
            <div class="dash_list">
                <div class="dash_item">
                    <a href="/crons">
                        <i class="fas fa-sync-alt"></i> Update Products
                    </a>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
<?php end_db_con(); ?>

<style>
    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
    }

    .dash_section {
        width: 100%;
        margin: 15px 0;
    }

    .dash_head {
        background: #eee;
        width: 100%;
        text-align: center;
        padding: 5px;
        margin: 10px 0;
        letter-spacing: 0.5px;
    }

    .dash_list {
        display: flex;
        width: 100%;
        justify-content: space-around
    }

    .dash_item a {
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
        font-weight: bold;
        /* Remove anchor Style */
        text-decoration: inherit;
        color: inherit;
        cursor: pointer;
    }

    .dash_item a:hover {
        font-size: 1.5rem;
        color: green;
        margin: 10px;
        align-items: center;

    }

    i {
        font-size: 40px;
        margin: 8px auto;
    }

    rs {
        letter-spacing: 1px;
    }

    rs::before {
        content: 'Rs.';
    }
</style>

<?php require '../inc/footer.php'; ?>