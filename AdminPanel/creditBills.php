<?php
include 'nav.php';
require_once(__DIR__ . '../../inc/config.php');
require_once(__DIR__ . '../../inc/header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Purchase Bills</title>
</head>

<body>

    <center>
        <h1>Credit Bills</h1>
    </center>

    <?php
    $sql = "SELECT * FROM `purchase` WHERE `balance_payment` > 0 ORDER BY `purchase`.`balance_payment_date` ASC, `purchase`.`balance_payment` ASC, `purchase`.`purchase_date` ASC; ";

    // purchase_id
    // item_name
    // single_unit_cost
    // item_qty
    // payment_account
    // supplier
    // purchased_packet_qty
    // quantity_per_packet
    // packet_price
    // first_payment
    // balance_payment_date
    // balance_payment
    // bill_total
    // bill_payment_type
    // packet_purchase
    // purchase_date
    // purchase_time


    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table id='DataTable'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>id</th>";
            echo "<th>Item Name</th>";
            echo "<th>Single Unit Cost</th>";
            echo "<th>Item Quantity</th>";
            echo "<th>Payment Account</th>";
            echo "<th>Supplier</th>";
            echo "<th>Purchased Packet Quantity</th>";
            echo "<th>Quantity Per Packet</th>";
            echo "<th>Packet Price</th>";
            echo "<th>First Payment</th>";
            echo "<th>Balance Payment Date</th>";
            echo "<th>Balance Payment</th>";
            echo "<th>Bill Total</th>";
            echo "<th>Bill Payment Type</th>";
            echo "<th>Packet Purchase</th>";
            echo "<th>Purchase Date</th>";
            echo "<th>Purchase Time</th>";
            echo "<th>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                $purchase_id = $row['purchase_id'];
                $item_name = $row['item_name'];
                $single_unit_cost = $row['single_unit_cost'];
                $item_qty = $row['item_qty'];
                $payment_account = $row['payment_account'];
                $supplier = $row['supplier'];
                $purchased_packet_qty = $row['purchased_packet_qty'];
                $quantity_per_packet = $row['quantity_per_packet'];
                $packet_price = $row['packet_price'];
                $first_payment = $row['first_payment'];
                $balance_payment_date = $row['balance_payment_date'];
                $balance_payment = $row['balance_payment'];
                $bill_total = $row['bill_total'];
                $bill_payment_type = $row['bill_payment_type'];
                $packet_purchase = $row['packet_purchase'];
                $purchase_date = $row['purchase_date'];
                $purchase_time = $row['purchase_time'];

                echo "<tr>";
                echo "<td>$purchase_id</td>";
                echo "<td>$item_name</td>";
                echo "<td>$single_unit_cost</td>";
                echo "<td>$item_qty</td>";
                echo "<td>$payment_account</td>";
                echo "<td>$supplier</td>";
                echo "<td>$purchased_packet_qty</td>";
                echo "<td>$quantity_per_packet</td>";
                echo "<td>$packet_price</td>";
                echo "<td>$first_payment</td>";
                echo "<td>$balance_payment_date</td>";
                echo "<td>$balance_payment</td>";
                echo "<td>$bill_total</td>";
                echo "<td>$bill_payment_type</td>";
                echo "<td>$packet_purchase</td>";
                echo "<td>$purchase_date</td>";
                echo "<td>$purchase_time</td>";
                // Pay & Delete onclick Buttons

                echo "<td>";
                echo "<button onclick='payBill($purchase_id, `$item_name`, $balance_payment, `$supplier` )'>Pay</button> <br>";
                echo "<button onclick='deleteBill($purchase_id)'>Delete</button>";
                echo "</td>";
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

</html>

<script>
    // Swal.fire({
    //     title: 'Edit Cash In Hand Amount',
    //     html: `<label for = "new_cash_in_hand_amount"> Amount : </label>
    //                 <input type="number" id="new_cash_in_hand_amount" value="${cash_in_hand_amount}" class="swal2-input">`,
    //     showCancelButton: true,
    //     confirmButtonText: 'Update',
    //     showLoaderOnConfirm: true,
    //     preConfirm: () => {
    //         const new_cash_in_hand_amount = Swal.getPopup().querySelector('#new_cash_in_hand_amount').value;
    //         if (!new_cash_in_hand_amount) {
    //             Swal.showValidationMessage(`Please enter amount`);
    //         }
    //         return fetch(`/AdminPanel/api/edit_cash_in_hand.php?new_cash_in_hand_amount=${new_cash_in_hand_amount}`)
    //             .then(response => {
    //                 if (!response.ok) {
    //                     throw new Error(response.statusText)
    //                 }
    //                 return response.json()
    //             })
    //             .catch(error => {
    //                 Swal.showValidationMessage(`Request failed: ${error}`)
    //             })
    //     },
    //     allowOutsideClick: () => !Swal.isLoading()
    // }).then((result) => {
    //     if (result.isConfirmed) {
    //         Swal.fire({
    //             title: `Cash In Hand Balance Updated Successfully`,
    //             icon: 'success',
    //             timer: 1000
    //         }).then(() => {
    //             location.reload();
    //         });
    //     }
    // });

    function payBill(purchase_id, item_name, balance_payment, supplier) {
        Swal.fire({
            title: `Pay Bill for ${item_name}`,
            html: `
            Balance Payment : ${balance_payment} <br>
            Supplier : ${supplier} <br>
            <label for = "payAmount"> Pay Amount : </label>
                    <input type="number" id="payAmount" value="${balance_payment}" class="swal2-input"> <br>
            <label for="paymentAccount">Payment Account :</label>
                <select id="paymentAccount"> <?php echo implode('', $bankAccountsOptionList); ?> </select>`,
            showCancelButton: true,
            confirmButtonText: 'Pay',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const payAmount = Swal.getPopup().querySelector('#payAmount').value;
                if (!payAmount) {
                    Swal.showValidationMessage(`Please enter amount`);
                }
                return fetch(`/AdminPanel/api/payCreditBill.php?purchase_id=${purchase_id}&payAmount=${payAmount}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`)
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: `Bill Paid Successfully`,
                    icon: 'success',
                    timer: 1000
                }).then(() => {
                    location.reload();
                });
            }
        });
    }

    function deleteBill(purchase_id) {
        Swal.fire({
            title: `Delete Bill`,
            html: `Are you sure you want to delete this credit bill?`,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/AdminPanel/api/deleteCreditBill.php?purchase_id=${purchase_id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`)
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: `Bill Deleted Successfully`,
                    icon: 'success',
                    timer: 1000
                }).then(() => {
                    location.reload();
                });
            }
        });
    }
</script>