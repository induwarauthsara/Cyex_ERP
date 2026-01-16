<?php
require_once 'inc/config.php';

echo "Running Migration 001...\n";

// Read SQL file
$sqlFile = '001_add_company_settings.sql';
if (!file_exists($sqlFile)) {
    die("Migration file not found!");
}

$sqlContent = file_get_contents($sqlFile);
$queries = explode(';', $sqlContent);

foreach ($queries as $query) {
    echo "Processing query...\n";
    $query = trim($query);
    if (!empty($query)) {
        if (mysqli_query($con, $query)) {
            echo "Success.\n";
        } else {
            echo "Error: " . mysqli_error($con) . "\n";
            // Don't die, as some might duplicate key errors if repeated
        }
    }
}

echo "Migration Complete.\n";
?>
