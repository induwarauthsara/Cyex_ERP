<?php
/**
 * Printer Usage API Endpoint
 * Returns printer usage statistics
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

$printers = [];
$counts = [];

// Connect to printer counter database
$printerDB = mysqli_connect($server, "srijayalk_shopprintercounter", "srijayalk_shopprintercounter", "srijayalk_shopprintercounter");

if ($printerDB) {
    // Try to get printer usage data
    // First check if main tables exist
    $sql = "SELECT 
                p.printerName,
                SUM(c.count) as total_count
            FROM printer_counter_count c
            INNER JOIN printer_counter_types t ON c.typeID = t.typeID
            INNER JOIN printer_counter_printers p ON t.printerID = p.printerID
            WHERE c.date BETWEEN '$startDate' AND '$endDate'
            GROUP BY p.printerID, p.printerName
            ORDER BY total_count DESC";
    
    $result = mysqli_query($printerDB, $sql);
    
    if (!$result) {
        // Try alternative structure (original table names)
        $sql = "SELECT 
                    p.printerName,
                    SUM(c.count) as total_count
                FROM `count` c
                INNER JOIN `types` t ON c.typeID = t.typeID
                INNER JOIN `printers` p ON t.printerID = p.printerID
                WHERE c.date BETWEEN '$startDate' AND '$endDate'
                GROUP BY p.printerID, p.printerName
                ORDER BY total_count DESC";
        $result = mysqli_query($printerDB, $sql);
    }
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $printers[] = $row['printerName'];
            $counts[] = intval($row['total_count']);
        }
    }
    
    mysqli_close($printerDB);
}

// If no data, return placeholder
if (empty($printers)) {
    $printers = ['No Printer Data'];
    $counts = [0];
}

echo json_encode([
    'printers' => $printers,
    'counts' => $counts
]);
?>
