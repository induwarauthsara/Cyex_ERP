<?php
include '../nav.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Accounts</title>

    <style>
        h2 {
            color: white;
        }

        .add_pettycash {
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .buttons {
            width: 100%;
            display: flex;
            justify-content: center;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="buttons">
        <button class="add_pettycash" onclick="add_bankDeposit(true)">Add Bank Deposit </button>
        <button class="add_pettycash" onclick="add_bankAccount()">Create New Bank Account</button>
    </div>
    <div id="cards">
        <h2> Bank Summery : </h2>
        <div class="card">
            <i class="fa-solid fa-university"></i>
            <div class="info">
                <h3>Total Bank Balance</h3>
                <h2><?php
                    $sql = "SELECT sum(amount) FROM `accounts` WHERE account_type = 'bank';";
                    $total_bank_balance = mysqli_fetch_assoc(mysqli_query($con, $sql))['sum(amount)'] ?? 0;
                    echo number_format($total_bank_balance, 2);
                    ?></h2>
            </div>
        </div>
        <div class="card">
            <i class="fa-solid fa-piggy-bank"></i>
            <div class="info">
                <h3>Today Bank Deposit</h3>
                <h2><?php
                    $sql = "SELECT SUM(amount) FROM `bank_deposits` WHERE deposit_date = CURDATE();";
                    $today_bank_deposit = mysqli_fetch_assoc(mysqli_query($con, $sql))['SUM(amount)'] ?? 0;
                    echo number_format($today_bank_deposit, 2);
                    ?></h2>
            </div>
        </div>
    </div>

    <div id="cards">
        <h2> Banks Accounts : </h2>
        <?php
        $sql = "SELECT account_name, amount FROM accounts WHERE account_type = 'bank';";
        $result = mysqli_query($con, $sql);
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $account_name = $row['account_name'];
                $amount = $row['amount'] ?? 0;

                echo "<div class='card'>";
                echo "<i class='fa-solid fa-university'></i>";
                echo "<div class='info'>";
                echo "<h3>$account_name</h3>";
                echo "<h2>" . number_format($amount, 2) . "</h2>";
                // Add Edit and Delete Button
                echo "<div class='buttons'>";
                echo "<button class='smallIcon' onclick='edit_bankAccount(`$account_name`, `$amount`, `$account_name`)'><i class='fa-solid fa-pen'></i></button>";
                echo "<button class='smallIcon' onclick='delete_bankAccount(`$account_name`, `$amount`)'><i class='fa-solid fa-trash'></i></button>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
        ?>
    </div>

    <!-- Bank Deposit Log with DataTable-->
    <table id="DataTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Bank Account</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Time</th>
                <th>Employee</th>
                <th>Edit</th>
                <th>Delete</th>

            </tr>
        </thead>
        <tbody>
            <?php
            // Get employee name
            // $sql = "SELECT bank_deposit_id, bank_account, amount, deposit_date, deposit_time, employee_id FROM bank_deposits;";
            $sql = "SELECT bank_deposits.bank_deposit_id, bank_deposits.bank_account, bank_deposits.amount, bank_deposits.deposit_date, bank_deposits.deposit_time, employees.emp_name 
                    FROM bank_deposits INNER JOIN employees ON bank_deposits.employee_id = employees.employ_id;";
            $result = mysqli_query($con, $sql);
            if ($result) {
                while ($row = mysqli_fetch_array($result)) {
                    $bank_deposit_id = $row['bank_deposit_id'];
                    $bank_account = $row['bank_account'];
                    $amount = $row['amount'];
                    $deposit_date = $row['deposit_date'];
                    $deposit_time = $row['deposit_time'];
                    $employee_name = $row['emp_name'];

                    echo "<tr>";
                    echo "<td>$bank_deposit_id</td>";
                    echo "<td>$bank_account</td>";
                    echo "<td>$amount</td>";
                    echo "<td>$deposit_date</td>";
                    echo "<td>$deposit_time</td>";
                    echo "<td>$employee_name</td>";
                    echo "<td><button onclick='edit_bankDeposit($bank_deposit_id, `$bank_account`, `$amount`, `$deposit_date`, `$deposit_time`, `$employee_name`)'>Edit</button></td>";
                    echo "<td><button onclick='delete_bankDeposit($bank_deposit_id, `$bank_account`, `$amount`)'>Delete</button></td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
</body>
<!-- ======================================== Add Bank Deposit ======================================== -->
<script src="/inc/add_bank_deposit_modal.js"></script>

<script>
    // =============================== Create New Bank Account =================================
    // Modal for Add Bank Account (input : Bank Account Name, Initial Amount)
    function add_bankAccount() {
        Swal.fire({
            title: 'Create New Bank Account',
            html: '<input id="bankAccountName" class="swal2-input" placeholder="Bank Account Name">' +
                '<input id="initialAmount" class="swal2-input" placeholder="Initial Amount">',
            confirmButtonText: 'Create',
            preConfirm: () => {
                const bankAccountName = Swal.getPopup().querySelector('#bankAccountName').value;
                const initialAmount = Swal.getPopup().querySelector('#initialAmount').value;
                if (bankAccountName && !isNaN(initialAmount)) {
                    return fetch(`/inc/add_bank_account.php?bankAccount=${bankAccountName}&amount=${initialAmount}`, {
                            method: 'GET'
                        })
                        .then(response => response.text())
                        .then(html => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Bank Account Created Successfully',
                                text: bankAccountName + ' Bank Account Created Successfully',
                                showValidationMessage: false,
                                timer: 2000
                            });
                            location.reload();
                        })
                        .catch(error => {
                            swil.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Error : ' + error,
                                showValidationMessage: false,
                                timer: 60000
                            });
                        });
                } else {
                    Swal.showValidationMessage(`Please enter Valid Bank Account Name and Initial Amount`);
                }
            }
        });
    }

    // ========================  Edit Bank Deposit =================================
    function edit_bankDeposit(bank_deposit_id, bankAccountName, amount, deposit_date, deposit_time) {
        fetch("/inc/fetch_bank_accounts.php")
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: 'Edit Bank Deposit',
                    html: '<label for="bankAccountName" class="swal2-label">Select Bank Account:</label>' +
                        '<select id="bankAccountName" class="swal2-input">' +
                        data.map(account => `<option value="${account}" ${account == bankAccountName ? 'selected' : ''}>${account}</option>`).join('') +
                        '</select>' +
                        '<label for="amount" class="swal2-label">Amount:</label>' +
                        '<input id="amount" class="swal2-input" value="' + amount + '">' +
                        '<label for="date" class="swal2-label">Date:</label>' +
                        '<input type="date" id="deposit_date" class="swal2-input" value="' + deposit_date + '">' +
                        '<label for="time" class="swal2-label">Time:</label>' +
                        '<input type="time" id="deposit_time" class="swal2-input" value="' + deposit_time + '">',
                    confirmButtonText: 'Update',
                    preConfirm: () => {
                        const bankAccountName = Swal.getPopup().querySelector('#bankAccountName').value;
                        const amount = Swal.getPopup().querySelector('#amount').value;
                        const deposit_date = Swal.getPopup().querySelector('#deposit_date').value;
                        const deposit_time = Swal.getPopup().querySelector('#deposit_time').value;



                        if (bankAccountName && !isNaN(amount) && deposit_date && deposit_time) {
                            return fetch(`bank/edit_bank_deposit.php?bank_deposit_id=${bank_deposit_id}&bankAccountName=${bankAccountName}&amount=${amount}&deposit_date=${deposit_date}&deposit_time=${deposit_time}`, {
                                    method: 'GET'
                                })
                                .then(response => response.text())
                                .then(html => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Bank Deposit Updated Successfully',
                                        text: bankAccountName + ' Bank Deposit Updated Successfully',
                                        showValidationMessage: false,
                                        timer: 2000
                                    });
                                    location.reload();
                                })
                                .catch(error => {
                                    swil.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Something went wrong. Error : ' + error,
                                        showValidationMessage: false,
                                        timer: 60000
                                    });
                                });
                        } else {
                            Swal.showValidationMessage(`Please enter Valid Bank Account Name, Amount, Date, Time and Employee Name`);
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

    // ======================== Delete Bank Deposit =================================
    function delete_bankDeposit(bank_deposit_id, bankAccountName, amount) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`bank/delete_bank_deposit.php?bank_deposit_id=${bank_deposit_id}&bankAccountName=${bankAccountName}&amount=${amount}`)
                    .then(response => response.text())
                    .then(html => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Bank Deposit Deleted Successfully',
                            text: 'Bank Deposit Deleted Successfully',
                            showValidationMessage: false,
                            timer: 2000
                        });
                        location.reload();
                    })
                    .catch(error => {
                        swil.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Error : ' + error,
                            showValidationMessage: false,
                            timer: 60000
                        });
                    });
            }
        });
    }

    // =!=!=!=!+!=!+!+=!=!=!=!+!=!+!+ Bank Accounts =!=!=!=!+!=!+!+=!=!=!=!+!=!+!+
    // ======================== Edit Bank Account =================================
    function edit_bankAccount(account_name, amount, previous_account_name) {
        Swal.fire({
            title: 'Edit Bank Account Details',
            html: '<label for="account_name" class="swal2-label">Bank Account Name:</label>' +
                '<input id="account_name" class="swal2-input" value="' + account_name + '">' +
                // Add Checkbox for 'Change Previous Record Bank Name'
                '<label for="change_previous_records" class="swal2-label">Change Previous Records Bank Name:</label>' +
                '<input type="checkbox" id="change_previous_records" class="swal3-input">' +
                '</br> </br><label for="amount" class="swal2-label">Current Balance:</label>' +
                '<input id="amount" class="swal2-input" value="' + amount + '">',
            confirmButtonText: 'Update',
            preConfirm: () => {
                const account_name = Swal.getPopup().querySelector('#account_name').value;
                const amount = Swal.getPopup().querySelector('#amount').value;
                const change_previous_records = Swal.getPopup().querySelector('#change_previous_records').checked;
                if (account_name && !isNaN(amount)) {
                    return fetch(`bank/edit_bank_account.php?account_name=${account_name}&amount=${amount}&change_previous_records=${change_previous_records}&previous_account_name=${previous_account_name}`, {
                            method: 'GET'
                        })
                        .then(response => response.text())
                        .then(html => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Bank Account Updated Successfully',
                                text: account_name + ' Bank Account Updated Successfully',
                                showValidationMessage: false,
                                timer: 2000
                            });
                            location.reload();
                        })
                        .catch(error => {
                            swil.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Error : ' + error,
                                showValidationMessage: false,
                                timer: 60000
                            });
                        });
                } else {
                    Swal.showValidationMessage(`Please enter Valid Bank Account Name and Amount`);
                }
            }
        });
    }

    // ======================== Delete Bank Account =================================
    function delete_bankAccount(account_name, amount) {
        // Ask "Delete Previous Records" or not as a Checkbox
        Swal.fire({
            title: `Are you sure to Delete ${account_name} Bank Account?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            html: '<input type="checkbox" id="deletePreviousRecords" /> <label for="deletePreviousRecords">Delete Previous Bank Deposit Records</label>',
            preConfirm: () => {
                return {
                    deletePreviousRecords: document.getElementById('deletePreviousRecords').checked
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const deletePreviousRecords = result.value.deletePreviousRecords;
                fetch(`bank/delete_bank_account.php?account_name=${account_name}&amount=${amount}&deletePreviousRecords=${deletePreviousRecords}`)
                    .then(response => response.text())
                    .then(html => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Bank Account Deleted Successfully',
                            text: 'Bank Account Deleted Successfully',
                            showValidationMessage: false,
                            timer: 2000
                        });
                        location.reload();
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Error : ' + error,
                            showValidationMessage: false,
                            timer: 60000
                        });
                    });
            }
        });
    }
</script>

</html>