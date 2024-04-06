<?php require_once 'config.php';

// Assuming you have a database connection established
// Perform a query to fetch the updated todo list from the database
$sql = "SELECT * FROM `todo` WHERE `status` = 1 ORDER BY `submision_time` ASC;";
$result = mysqli_query($con, $sql);

// Initialize an empty string to store the HTML for the todo list
$todoListHTML = '';

// Check if the query was successful and fetch the rows
if ($result) {
    // Loop through the rows and generate HTML for each todo item
    while ($row = mysqli_fetch_assoc($result)) {
        $todoID = $row['todo_id'];
        $invoice_number = $row['invoice_number'];

        $todoListHTML .= "<div class='todoCard'>
    <div class='billNo'> <a href=/invoice/edit-bill.php?id=$invoice_number>" . $invoice_number . "</a></div>
    <h3 class='todoTitle'>" . $row['title'] . "</h3>
    <div class='todoTime'>" . $row['submision_time'] . "</div>
    <div class='todoComplete' onclick='complete_todo($todoID)'>Complete</div>
 </div>";
    }
}

// Return the generated HTML for the todo list
echo $todoListHTML;
