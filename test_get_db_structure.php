<h1>DB Stucture </h1>
<?php
require_once 'inc/config.php';
// Query to fetch tables and their columns
$sql = "
    SELECT TABLE_NAME, COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'globalMartERP'
    ORDER BY TABLE_NAME, ORDINAL_POSITION;
";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    $tables = [];
    while ($row = $result->fetch_assoc()) {
        $tables[$row['TABLE_NAME']][] = $row['COLUMN_NAME'];
    }

    // Generate formatted output
    foreach ($tables as $table => $columns) {
        echo "$table(" . implode(", ", $columns) . ")\n";
        echo "<br>";
    }
} else {
    echo "No tables found in the database.";
}

// Close the connection
$con->close();
?>