<?php
include 'nav.php';
// enum('uncleared', 'skip', 'cleared')	
if (isset($_GET['s'])) {
    $status = $_GET['s'];
    if ($status == 'All') {
        $sqlSearch = "SELECT oneTimeProducts_sales.*, invoice.customer_name FROM oneTimeProducts_sales INNER JOIN invoice WHERE oneTimeProducts_sales.invoice_number = invoice.invoice_number;";
    } elseif ($status == 'Uncleared') {
        $sqlSearch = "SELECT oneTimeProducts_sales.*, invoice.customer_name FROM oneTimeProducts_sales INNER JOIN invoice WHERE oneTimeProducts_sales.invoice_number = invoice.invoice_number AND oneTimeProducts_sales.status = 'uncleared'";
    } elseif ($status == 'Skipped') {
        $sqlSearch = "SELECT oneTimeProducts_sales.*, invoice.customer_name FROM oneTimeProducts_sales INNER JOIN invoice WHERE oneTimeProducts_sales.invoice_number = invoice.invoice_number AND oneTimeProducts_sales.status = 'skip';";
    } elseif ($status == 'Cleared') {
        $sqlSearch = "SELECT oneTimeProducts_sales.*, invoice.customer_name FROM oneTimeProducts_sales INNER JOIN invoice WHERE oneTimeProducts_sales.invoice_number = invoice.invoice_number AND oneTimeProducts_sales.status = 'cleared';";
    } else {
        $status = 'Uncleared';
        $sqlSearch = "SELECT oneTimeProducts_sales.*, invoice.customer_name FROM oneTimeProducts_sales INNER JOIN invoice WHERE oneTimeProducts_sales.invoice_number = invoice.invoice_number AND oneTimeProducts_sales.status = 'uncleared';";
    }
} else {
    $status = 'Uncleared';
    $sqlSearch = "SELECT oneTimeProducts_sales.*, invoice.customer_name FROM oneTimeProducts_sales INNER JOIN invoice WHERE oneTimeProducts_sales.invoice_number = invoice.invoice_number AND oneTimeProducts_sales.status = 'uncleared';";
}
echo "<h1> $status One-Time Products List </h1>";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Time Products</title>

</head>

<body>
    <div id="errors">
        <?php
        $sql = "SELECT COUNT(*) FROM `oneTimeProducts_sales` WHERE `status` = 'uncleared';";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $uncleared_oneTimeProducts_count = mysqli_fetch_array($result);
            $uncleared_oneTimeProducts_count = $uncleared_oneTimeProducts_count['COUNT(*)'];
            if ($uncleared_oneTimeProducts_count > 0) {
                echo "<p><b> You have  $uncleared_oneTimeProducts_count  Uncleared One-Time-Products. <a href='one_time_products.php'>Solve them NOW !</a>  </b></p>";
            }
        } else {
            echo "Database Query Failed";
        }
        ?>
    </div>

    <form action="" method="get">
        <input type="submit" value="All" name="s">
        <input type="submit" value="Uncleared" name="s">
        <input type="submit" value="Skipped" name="s">
        <input type="submit" value="Cleared" name="s">
    </form>

    <table id="DataTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>id</th>
                <th>invoice_number</th>
                <th>Product Name</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Cost</th>
                <th>Profit</th>
                <th>Status</th>
                <th>Supplier</th>
                <th>Account</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = mysqli_query($con, $sqlSearch);
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    while ($oneTimeProducts = mysqli_fetch_array($result)) {
                        $OTProduct_id = $oneTimeProducts['oneTimeProduct_id'];
                        $OTProduct_invoice_number = $oneTimeProducts['invoice_number'];
                        $customer_name = $oneTimeProducts['customer_name'];
                        $OTProduct_product = $oneTimeProducts['product'];
                        $OTProduct_qty = $oneTimeProducts['qty'];
                        $OTProduct_rate = $oneTimeProducts['rate'];
                        $OTProduct_amount = $oneTimeProducts['amount'];
                        $OTProduct_cost = $oneTimeProducts['cost'];
                        $OTProduct_profit = $oneTimeProducts['profit'];
                        $OTProduct_status = $oneTimeProducts['status'];
                        $OTProduct_supplier = $oneTimeProducts['supplier'];
                        $OTProduct_account = $oneTimeProducts['account_name'];
                        $action = '';

                        if ($oneTimeProducts['status'] == 'uncleared') {
                            $action = "<button onclick='solveProduct(`$OTProduct_id`, `$OTProduct_invoice_number`, `$customer_name`, `$OTProduct_product`, `$OTProduct_qty`, `$OTProduct_rate`, `$OTProduct_amount`)'>Solve</button> <br>
                            <button onclick='skipProduct(`$OTProduct_id`)'> Skip </button>";
                        } else {
                            $action = "<button onclick='solveProduct(`$OTProduct_id`, `$OTProduct_invoice_number`, `$customer_name`, `$OTProduct_product`, `$OTProduct_qty`, `$OTProduct_rate`, `$OTProduct_amount`, `$OTProduct_cost`, `$OTProduct_profit`, `$OTProduct_status`, `$OTProduct_supplier`, `$OTProduct_account`)'>Edit</button>";
                        }
                        $OTProduct_account = $OTProduct_account == 'cash_in_hand' ? 'Cash in Hand' : $OTProduct_account;
                        echo "<tr>";
                        echo "<td>" . $OTProduct_id . "</td>";
                        echo "<td>" . $OTProduct_invoice_number . "</td>";
                        echo "<td>" . $OTProduct_product . "</td>";
                        echo "<td>" . $OTProduct_qty . "</td>";
                        echo "<td>" . $OTProduct_rate . "</td>";
                        echo "<td>" . $OTProduct_amount . "</td>";
                        echo "<td>" . $OTProduct_cost . "</td>";
                        echo "<td>" . $OTProduct_profit . "</td>";
                        echo "<td>" . $OTProduct_status . "</td>";
                        echo "<td>" . $OTProduct_supplier . "</td>";
                        echo "<td>" . $OTProduct_account . "</td>";
                        echo "<td>" . $action . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "No any One Time Products";
                }
            } else {
                echo "Database Query Failed";
            }
            ?>
        </tbody>

</body>
<script>
    //Total cost
    //     Total Cost 
    //         Profit = Amount - Total Cost
    //     Supplier Name

    // Each Item Cost
    //     Item Cost
    //         Profit = Amount - (Item Cost x Quantity)
    //     Supplier Name

    function solveProduct(id, invoice_id, customer_name, product_name, qty, rate, amount, cost, profit, status, supplier, CashOutAccount) {
        if (typeof cost !== 'undefined') {
            // Product Edit
            var OTPEdit = true;
            cost = parseFloat(cost).toFixed(2);
            profit = parseFloat(profit).toFixed(2);
            var EachCostValue = cost / qty;
            EachCostValue = parseFloat(EachCostValue).toFixed(2);
        } else {
            var OTPEdit = false; // Product Solve
            var cost = '';
            var profit = '';
            var EachCostValue = '';
            var supplier = '';
            var CashOutAccount = '';
        }
        fetch("/inc/fetch_bank_accounts.php")
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: `${OTPEdit ? 'Edit' : 'Solve'} One Time Product`,
                    html: `
                                <div class="tab">
                                    <button class="tablinks active" onclick="openTab(event, 'TotalCost')">Total Cost</button>
                                    <button class="tablinks" onclick="openTab(event, 'EachItemCost')">Each Item Cost</button>
                                </div>

                                <div id="TotalCost" class="tabcontent" style="display:block; margin-top: 20px;">
                                <label for="total-cost"> Total Cost : </label>
                                <input id="total-cost" class="swal2-input" placeholder="Total Product Cost" value="${cost}">
                                </div>
                                
                                <div id="EachItemCost" class="tabcontent" style="display:none; margin-top: 20px;">
                                    <label for="each-cost"> Each Item Cost : </label>
                                    <input id="each-cost" class="swal2-input" placeholder="Each Item Cost" value="${EachCostValue}">
                                    <label for="qty"> Quantity : <b>${qty}</b></label>
                                    <input id="qty" class="swal2-input" placeholder="Quantity" value="${qty}" hidden> <br><br>
                                </div>

                                    <label for="account"> Cash Out Account : </label>
                                    <select id="account" class="swal2-input" style="margin: 0;">
                                    <option value="cash_in_hand" ${OTPEdit ? '' : 'selected'}>Cash in Hand</option>
                                    ${data.map(account => `<option value="${account}" ${account == CashOutAccount ? 'selected' : ''}>${account}</option>`).join('')}            
                                    </select><br><br>
                                
                                Customer Name : <b>${customer_name}</b> <br>

                                <label for="amount"> Cash In : Rs. <b>${amount}</b> </label><br><br>
                                <input id="amount" class="swal2-input" value="${amount}" hidden>
                                
                                <div id="profit-calculation">
                                <label for="profit"> Profit : </label>
                                    <p id="calculation-steps"></p>
                                    <input id="profit" class="swal2-input" placeholder="Profit" value="${profit}" ${OTPEdit ? 'readonly' : 'hidden'}><br><br>
                                </div>

                                <label for="supplier"> Supplier Name :</label>
                                <input type="text" id="supplier" placeholder="Supplier Name" class="swal2-input" value="${supplier}">
                            `,
                    focusConfirm: false,
                    didOpen: () => {
                        // Add event listeners to input fields for real-time calculation
                        document.getElementById('total-cost').addEventListener('input', calculateProfit);
                        document.getElementById('each-cost').addEventListener('input', calculateProfit);
                        document.getElementById('qty').addEventListener('input', calculateProfit);
                    },
                    preConfirm: () => {
                        const activeTab = document.querySelector('.tablinks.active').innerText;
                        const isTotal = activeTab === 'Total Cost';
                        const isEachItem = activeTab === 'Each Item Cost';

                        const supplier = document.getElementById('supplier').value || 'Unknown';
                        const profit = document.getElementById('profit').value || 0;
                        const account = document.getElementById('account').value;

                        var total_cost = parseFloat(document.getElementById('total-cost').value);

                        const each_cost = parseFloat(document.getElementById('each-cost').value);
                        const quantity = parseInt(document.getElementById('qty').value);


                        if ((isTotal && !isNaN(total_cost)) || isEachItem && !isNaN(each_cost) && !isNaN(quantity)) {
                            total_cost = isEachItem ? each_cost * quantity : total_cost;

                            return fetch(`/AdminPanel/OneTimeProduct/solve.php?id=${id}&supplier=${supplier}&cost=${total_cost}&profit=${profit}&tab=${activeTab}&account=${account}&invoiceID=${invoice_id}&edit=${OTPEdit}`, {
                                    method: 'Get'
                                }).then(response => {
                                    if (!response.ok) {
                                        throw new Error(response.statusText)
                                    } else {
                                        Swal.fire({
                                            title: 'Solved',
                                            text: 'One Time Product Solved Successfully',
                                            icon: 'success',
                                            timer: 2000
                                        });
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1900); // 3-second delay before reloading
                                    }
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(`Request failed: ${error}`)
                                });
                        } else {
                            Swal.showValidationMessage(`Please fill all the required fields with valid numbers`);
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong! Error : ' + error,
                });
            });
    }



    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";

        // Reset and recalculate profit when switching tabs
        document.getElementById('profit').value = '';
        document.getElementById('calculation-steps').textContent = '';
        calculateProfit();
    }

    function calculateProfit() {
        // Selected tab
        const selected_tab = document.querySelector('.tablinks.active').innerText;

        const total_cost = document.getElementById('total-cost').value;
        const each_cost = document.getElementById('each-cost').value;
        const qty = document.getElementById('qty').value;
        const amount = document.getElementById('amount').value;

        if (selected_tab === 'Total Cost') {
            if (total_cost !== '' && !isNaN(total_cost)) {
                var profit = amount - total_cost;
                var calculationSteps = `Profit = Cash In - Total Product Cost<br><b>Profit </b> = ${amount} - ${total_cost} = <h2 style='display: inline;'>Rs. ${profit}</h2> `;
                document.getElementById('profit').value = profit.toFixed(2);
                document.getElementById('calculation-steps').innerHTML = calculationSteps;
            }
        } else if (selected_tab === 'Each Item Cost') {
            if (each_cost !== '' && !isNaN(each_cost) && qty !== '' && !isNaN(qty)) {
                var profit = amount - (each_cost * qty);
                var calculationSteps = `Profit = Cash In - (Each Item Cost × Quantity) <br> <b>Profit </b> = ${amount} - (${each_cost} × ${qty}) = <h2 style='display: inline;'>Rs. ${profit}</h2>`;
                document.getElementById('profit').value = profit.toFixed(2);
                document.getElementById('calculation-steps').innerHTML = calculationSteps;
            }
        }
    }

    // Skip Product
    function skipProduct(id) {
        Swal.fire({
            title: 'Skip One Time Product',
            text: 'Are you sure you want to skip this One Time Product? Cost=0 and Profit=100%',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Skip it!',
            cancelButtonText: 'No, Keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/AdminPanel/OneTimeProduct/skip.php?id=${id}`, {
                        method: 'Get'
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        } else {
                            Swal.fire({
                                title: 'Skipped',
                                text: 'One Time Product Skipped Successfully',
                                icon: 'success',
                                timer: 2000
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1900); // 3-second delay before reloading
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong! Error : ' + error,
                        });
                    });
            }
        });
    }

    // ======================================== Edit Product ========================================
    // This function replace bu SolveProduct function
    // function editProduct(id, invoice_id, customer_name, product_name, qty, rate, amount, cost, profit, status, supplier) {
    //     var EachCostValue = cost / qty;
    //     cost = cost.toFixed(2)
    //     profit = profit.toFixed(2);
    //     EachCostValue = EachCostValue.toFixed(2);
    //     Swal.fire({
    //         title: 'Edit One Time Product',
    //         html: `
    //         <div class="tab">
    //             <button class="tablinks active" onclick="openTab(event, 'TotalCost')">Total Cost</button>
    //             <button class="tablinks" onclick="openTab(event, 'EachItemCost')">Each Item Cost</button>
    //         </div>

    //         <div id="TotalCost" class="tabcontent" style="display:block; margin-top: 20px;">
    //         <label for="total-cost"> Total Cost : </label>
    //         <input id="total-cost" class="swal2-input" placeholder="Total Product Cost" value="${cost}">
    //         </div>

    //         <div id="EachItemCost" class="tabcontent" style="display:none; margin-top: 20px;">
    //             <label for="each-cost"> Each Item Cost : </label>
    //             <input id="each-cost" class="swal2-input" placeholder="Each Item Cost" value="${EachCostValue}">
    //             <label for="qty"> Quantity : <b>${qty}</b></label>
    //             <input id="qty" class="swal2-input" placeholder="Quantity" value="${qty}" hidden> <br><br>
    //         </div>

    //         <label for="account"> Cash Out Account : </label>
    //             <select id="account" class="swal2-input" style="margin: 0;">
    //             <option value="cash_in_hand" selected>Cash in Hand</option>
    //             ${data.map(account => `<option value="${account}">${account}</option>`).join('')}            
    //         </select><br><br>

    //         Customer Name : <b>${customer_name}</b> <br>

    //         <label for="amount"> Cash In : Rs. <b>${amount}</b> </label><br><br>
    //         <input id="amount" class="swal2-input" value="${amount}" hidden>

    //         <div id="profit-calculation">
    //         <label for="profit"> Profit : </label>
    //             <p id="calculation-steps"></p>
    //             <input id="profit" class="swal2-input" placeholder="Profit" value="${profit}" readonly><br><br>
    //         </div>

    //         <label for="supplier"> Supplier Name :</label>
    //         <input type="text" id="supplier" placeholder="Supplier Name" class="swal2-input" value="${supplier}">
    //     `,
    //         focusConfirm: false,
    //         didOpen: () => {
    //             // Add event listeners to input fields for real-time calculation
    //             document.getElementById('total-cost').addEventListener('input', calculateProfit);
    //             document.getElementById('each-cost').addEventListener('input', calculateProfit);
    //             document.getElementById('qty').addEventListener('input', calculateProfit);
    //         },
    //         preConfirm: () => {
    //             const activeTab = document.querySelector('.tablinks.active').innerText;
    //             const isTotal = activeTab === 'Total Cost';
    //             const isEachItem = activeTab === 'Each Item Cost';

    //             const supplier = document.getElementById('supplier').value || 'Unknown';
    //             const profit = document.getElementById('profit').value || 0;

    //             var total_cost = parseFloat(document.getElementById('total-cost').value);

    //             const each_cost = parseFloat(document.getElementById('each-cost').value);
    //             const quantity = parseInt(document.getElementById('qty').value);


    //             if ((isTotal && !isNaN(total_cost)) || isEachItem && !isNaN(each_cost) && !isNaN(quantity)) {
    //                 total_cost = isEachItem ? each_cost * quantity : total_cost;

    //                 return fetch(`/AdminPanel/OneTimeProduct/solve.php?id=${id}&supplier=${supplier}&cost=${total_cost}&profit=${profit}&tab=${activeTab}&invoiceID=${invoice_id}`, {
    //                         method: 'Get'
    //                     }).then(response => {
    //                         if (!response.ok) {
    //                             throw new Error(response.statusText)
    //                         } else {
    //                             Swal.fire({
    //                                 title: 'Solved',
    //                                 text: 'One Time Product Solved Successfully',
    //                                 icon: 'success',
    //                                 timer: 2000
    //                             });
    //                             setTimeout(() => {
    //                                 location.reload();
    //                             }, 1900); // 3-second delay before reloading
    //                         }
    //                     })
    //                     .catch(error => {
    //                         Swal.showValidationMessage(`Request failed: ${error}`)
    //                     });
    //             } else {
    //                 Swal.showValidationMessage(`Please fill all the required fields with valid numbers`);
    //             }


    //             return {
    //                 supplier,
    //                 cost: total_cost,
    //                 profit,
    //                 activeTab
    //             };
    //         }
    //     });
    // }

    // 
</script>

</html>