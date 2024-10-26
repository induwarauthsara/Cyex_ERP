<?php
include '../nav.php';

// Function to calculate incomes and expenses for a given period
function calculateFinancials($con, $startDate, $endDate)
{
    // Income for "Invoice - Cash In"
    $invoiceIncomeQuery = "SELECT 
                               SUM(amount) as total 
                           FROM 
                               transaction_log 
                           WHERE 
                                (transaction_type = 'Invoice - Cash In' OR transaction_type = 'Add Invoice Balance Payment') 
                                AND transaction_date BETWEEN '$startDate' AND '$endDate'";

    $invoiceIncomeResult = mysqli_query($con, $invoiceIncomeQuery);
    $invoiceIncome = mysqli_fetch_assoc($invoiceIncomeResult)['total'] ?? 0; // Handle null case

    // Income for "Repair Invoice - Cash In"
    $repairInvoiceIncomeQuery = "SELECT 
                                     SUM(amount) as total 
                                 FROM 
                                     transaction_log 
                                 WHERE 
                                     transaction_type = 'Repair Invoice - Cash In' 
                                     AND transaction_date BETWEEN '$startDate' AND '$endDate'";

    $repairInvoiceIncomeResult = mysqli_query($con, $repairInvoiceIncomeQuery);
    $repairInvoiceIncome = mysqli_fetch_assoc($repairInvoiceIncomeResult)['total'] ?? 0; // Handle null case

    // Total Income is the sum of both
    $totalIncome = $invoiceIncome + $repairInvoiceIncome;

    // Expense breakdown
    $expensesQuery = "SELECT 
                        transaction_type, SUM(amount) as total 
                      FROM 
                        transaction_log 
                      WHERE 
                        (transaction_type = 'Petty Cash' OR 
                         transaction_type = 'Raw Item Purchase' OR 
                         transaction_type LIKE 'Fall OneTimeProduct Cost from%' OR 
                         transaction_type = 'Salary Payment') 
                      AND transaction_date BETWEEN '$startDate' AND '$endDate' 
                      GROUP BY transaction_type";

    $expensesResult = mysqli_query($con, $expensesQuery);
    $expenseBreakdown = [];
    while ($row = mysqli_fetch_assoc($expensesResult)) {
        $expenseBreakdown[$row['transaction_type']] = $row['total'];
    }

    // Calculate total expenses
    $totalExpenses = array_sum($expenseBreakdown);

    // Get Salary Payments from both tables
    $salaryQuery1 = "SELECT SUM(amount) as total 
                     FROM transaction_log 
                     WHERE transaction_type = 'Salary Payment' 
                     AND transaction_date BETWEEN '$startDate' AND '$endDate'";

    $salaryResult1 = mysqli_query($con, $salaryQuery1);
    $salary1 = mysqli_fetch_assoc($salaryResult1)['total'] ?? 0; // Handle null case

    $salaryQuery2 = "SELECT SUM(amount) as total 
                     FROM salary 
                     WHERE description = 'Salary Paid' 
                     AND date BETWEEN '$startDate' AND '$endDate'";

    $salaryResult2 = mysqli_query($con, $salaryQuery2);
    $salary2 = mysqli_fetch_assoc($salaryResult2)['total'] ?? 0; // Handle null case

    // Get the lowest value for salary payment
    $salaryPayment = min($salary1, $salary2);

    // Add Salary Payment to expenses
    $expenseBreakdown['Salary Payment'] = $salaryPayment;
    $totalExpenses += $salaryPayment;

    // Calculate profit
    $profit = $totalIncome + $totalExpenses;

    return [
        'incomeBreakdown' => [
            'Invoice - Cash In' => $invoiceIncome,
            'Repair Invoice - Cash In' => $repairInvoiceIncome
        ],
        'totalIncome' => $totalIncome,
        'expenseBreakdown' => $expenseBreakdown,
        'totalExpenses' => $totalExpenses,
        'profit' => $profit
    ];
}

// Get current date
$currentDate = new DateTime();

// Prepare results
$results = [];

// Loop through the current and previous months
for ($i = 0; $i < 6; $i++) {
    // Clone the current date to manipulate it
    $month = clone $currentDate;
    $month->modify("-$i month");

    // Get the first and last day of the month
    $firstDay = $month->modify('first day of this month')->format('Y-m-d');
    $lastDay = $month->modify('last day of this month')->format('Y-m-d');

    // Get the current day for the current month
    if ($i === 0) {
        $lastDay = $currentDate->format('Y-m-d');
    }

    // Calculate financials
    $financials = calculateFinancials($con, $firstDay, $lastDay);

    // Store the result
    $results[] = [
        'month' => $month->format('F Y'),
        'incomeBreakdown' => $financials['incomeBreakdown'],
        'totalIncome' => $financials['totalIncome'],
        'expenseBreakdown' => $financials['expenseBreakdown'],
        'totalExpenses' => $financials['totalExpenses'],
        'profit' => $financials['profit']
    ];
}

// Function to format numbers as currency
function formatCurrency($amount)
{
    return number_format($amount, 2, '.', ',');
}

// Display results
echo "<h2>Financial Overview Report</h2>";
foreach ($results as $result) {
    echo "<h3>{$result['month']}</h3>";
    echo "<h4>Income Breakdown:</h4>";
    echo "<table border='1'>
        <tr>
            <th>Type</th>
            <th>Amount</th>
        </tr>";
    foreach ($result['incomeBreakdown'] as $type => $amount) {
        echo "<tr>
            <td>{$type}</td>
            <td>" . formatCurrency($amount) . "</td>
        </tr>";
    }
    echo "<tr>
        <td><strong>Total Income</strong></td>
        <td><strong>" . formatCurrency($result['totalIncome']) . "</strong></td>
    </tr>";
    echo "</table>";

    echo "<h4>Expense Breakdown:</h4>";
    echo "<table border='1'>
        <tr>
            <th>Type</th>
            <th>Amount</th>
        </tr>";
    foreach ($result['expenseBreakdown'] as $type => $amount) {
        echo "<tr>
            <td>{$type}</td>
            <td>" . formatCurrency($amount) . "</td>
        </tr>";
    }
    echo "<tr>
        <td><strong>Total Expenses</strong></td>
        <td><strong>" . formatCurrency($result['totalExpenses']) . "</strong></td>
    </tr>";
    echo "</table>";

    echo "<h4>Profit: " . formatCurrency($result['profit']) . "</h4>";
    echo "<hr>";
    echo "<hr>";
    echo "<br>";
    echo "<br>";
}

// Close the database connection
mysqli_close($con);
?>

<style>
    body {
        color: white;
    }

    tr:nth-child(odd) {
        color: white;
    }

    tr:nth-child(even) {
        color: black !important;
    }
</style>