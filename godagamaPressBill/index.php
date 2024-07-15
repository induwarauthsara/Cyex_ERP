    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="/style.css">
    <title>Godagama Press Bill</title>


    <?php require_once '../inc/config.php';
    // require '../inc/header.php';
    include '../inc/DataTable_cdn.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="https://kit.fontawesome.com/dc35af580f.js" crossorigin="anonymous"></script>


    <!-- adding jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.5.1.js "></script>

    <!-- favicon -->
    <link rel="shortcut icon" href="../logo.png" type="image/x-icon">

    <!-- add main css file -->
    <link rel="stylesheet" href="/style.css">

    <br>
    <!-- // Wallet Balance -->
    <?php


    $sql = "SELECT SUM(amount) AS balance FROM godagamaPressCreditBill;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $balance = $row["balance"];
            $formatted_amount = number_format($balance, 2, '.', ',');
            echo "<h1> Godagama Credit Balance: " . $formatted_amount . "</h1><br>";
        }
    } else {
        echo "0 results";
    }
    ?>

    <!-- Add Expenses with sweetalert2 -->
    <buttons>
        <button class="" onclick="addexpenses('Payment')">Make Payment</button>
        <button class="add_credit" onclick="addexpenses('Credit')">Add Credit</button>
    </buttons>

    <script>
        function addexpenses(recordType) {
            Swal.fire({
                title: 'Add ' + recordType,
                html: '<input type="text" id="description" placeholder="Description" class="swal2-input">' +
                    '<input type="text" id="amount" placeholder="Amount" class="swal2-input">',
                showCancelButton: true,
                confirmButtonText: 'Add',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const amount = Swal.getPopup().querySelector('#amount').value
                    const description = Swal.getPopup().querySelector('#description').value
                    if (!amount || !description) {
                        Swal.showValidationMessage(`Please enter amount and description`)
                    }
                    return {
                        amount: amount,
                        description: description
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: `Amount: ${result.value.amount} Description: ${result.value.description}`,
                        icon: 'success'
                    })
                    $.ajax({
                        type: "POST",
                        url: "addexpenses.php",
                        data: {
                            amount: result.value.amount,
                            description: result.value.description,
                            type: recordType
                        },
                        success: function(response) {
                            console.log(response);
                            location.reload();
                        }
                    });
                }
            })
        }
    </script>

    <!-- Display Wallet Table using DataTable.js -->

    <?php
    $sql = "SELECT * FROM godagamaPressCreditBill ORDER BY `godagamaPressCreditBill`.`id` DESC;";

    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table id='DataTable'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Date</th>";
            echo "<th>Time</th>";
            echo "<th>Description</th>";
            echo "<th>Amount</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                $amount = number_format($row['amount'], 2, '.', ',');
                echo "<tr>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['time'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td class='amount'>" . $amount . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
        } else {
            echo "No records matching your query were found.";
        }
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    ?>
    </body>



    <style>
        h1 {
            text-align: center;
            margin: 50px;
            font-size: 3rem;
        }

        button {
            padding: 20px;
            background-color: green;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 0.3rem;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .add_credit {
            background-color: red;
        }

        buttons {
            display: flex;
            justify-content: center;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#DataTable .amount').each(function() {
                var amount = $(this).text().trim();
                if (amount.startsWith('-')) {
                    $(this).css('color', 'green');
                } else {
                    $(this).css('color', 'red');
                }
            });
        });
    </script>