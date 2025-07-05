<?php
include 'conn.php';

echo "<pre>";
print_r($_POST);
echo "</pre>";

// Read Past TotalCount from the database
$sql = "SELECT `typeID`, `totalCount` FROM `printer_counter_types`";
$result = $con->query($sql);

// Save the past total count in an associative array
$pastTotalCount = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $typeID = $row['typeID'];
        $count = $row['totalCount'];
        $pastTotalCount[$typeID] = $count;
    }
}
echo "pastTotalCount";
echo "<pre>";
print_r($pastTotalCount);
echo "</pre>";


// TodayTotalCount
$todayTotalCount = array();

// Printer Final Count
$printerFinalCount = array();

// Update the TotalCount in the database
foreach ($_POST as $typeID => $count) {
    // Escape values to prevent SQL injection (you should use prepared statements for better security)
    $typeID = $con->real_escape_string($typeID);
    $count = $con->real_escape_string($count);

    // Calculate the new total count
    $todayTotalCount[$typeID] = $count - $pastTotalCount[$typeID];

    // Update the total count in the database
    $sql = "UPDATE `printer_counter_types` SET `totalCount` = '$count' WHERE `typeID` = '$typeID'";
    if ($con->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $con->error;
    }

    // Push $count to printerFinalCount array
    $printerFinalCount[$typeID] = $count;

}
echo "todayTotalCount";
echo "<pre>";
print_r($todayTotalCount);
echo "</pre>";


// Extract values from the POST request
// $data = $_POST;

// get Today date to variable
$today = date("Y-m-d");

// Initialize arrays to store rows and typeID-cost mapping
$rows = array();
$typeCostMapping = array();

// Fetch the cost for each typeID from the cost table
$sqlCost = "SELECT `typeID`, `cost` FROM `printer_counter_types`";
$resultCost = $con->query($sqlCost);

if ($resultCost->num_rows > 0) {
    while ($rowCost = $resultCost->fetch_assoc()) {
        $typeCostMapping[$rowCost['typeID']] = $rowCost['cost'];
    }
}

$dayTotalCost = 0;

// Iterate through the POST data
foreach ($todayTotalCount as $typeID => $count) {
    // Escape values to prevent SQL injection (you should use prepared statements for better security)
    $typeID = $con->real_escape_string($typeID);
    $count = $con->real_escape_string($count);

    // Calculate the total cost
    $totalCost = $count * $typeCostMapping[$typeID];

    // Push printerFinalCount[$typeID] at the end of the row

    // Add the row to the array
    $rows[] = "('$typeID', '$today', '$count', '$totalCost', '$printerFinalCount[$typeID]')";

    // update the total cost for the day
    $dayTotalCost += $totalCost;
}

// Construct and execute the SQL query
$sql = "INSERT INTO `printer_counter_count`(`typeID`, `date`, `count`, `cost`, `printerFinalCount`) VALUES " . implode(', ', $rows);

if ($con->query($sql) === TRUE) {
    echo "Data inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}

echo "<pre>";
echo "today Total Cost: $dayTotalCost";
echo "</pre>";

echo "<br>";
echo "<b>Final Insert Rows </b>: ";
echo "<pre>";
print_r($rows);
echo "</pre>";

// Update the statistics table
$sql = "UPDATE `printer_counter_statistics` SET `amount` = `amount` + $dayTotalCost WHERE `name` = 'to_deposit_balance'";
if ($con->query($sql) === TRUE) {
    echo "Statistics updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}

// Close the database connection
$con->close();

// Redirect to the home page
header('Location: dailycost-Total.php');
