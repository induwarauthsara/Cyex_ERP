<?php
/**
 * Salary-Expense Synchronization Helper
 * Central logic for syncing employee salary payments with expense management
 * 
 * This file provides shared functions used across multiple salary/attendance endpoints
 * to ensure consistent expense tracking for employee salaries.
 * 
 * @package SrijayaERP
 * @version 1.0
 */

// This file should be included, not called directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    die('Direct access forbidden');
}

/**
 * Get or create "Salaries" expense category
 * 
 * @param mysqli $con Database connection
 * @return int Category ID
 */
function getSalaryCategory($con) {
    $cat_query = mysqli_query($con, "SELECT category_id FROM expense_categories WHERE category_name = 'Salaries'");
    
    if (mysqli_num_rows($cat_query) > 0) {
        $cat_row = mysqli_fetch_assoc($cat_query);
        return (int)$cat_row['category_id'];
    }
    
    // Create category if it doesn't exist
    mysqli_query($con, "INSERT INTO expense_categories (category_name, description, color_code, icon, status) 
                        VALUES ('Salaries', 'Employee salary payments', '#45B7D1', 'users', 1)");
    return (int)mysqli_insert_id($con);
}

/**
 * Calculate total salary earned by employee in a specific month
 * Sums all "Day Salary" entries from salary table
 * 
 * @param mysqli $con Database connection
 * @param int $emp_id Employee ID
 * @param string $month_year Month in 'Y-m' format (e.g., '2025-12')
 * @return float Total earned salary for the month
 */
function getMonthlyEarnedSalary($con, $emp_id, $month_year) {
    $sql = "SELECT COALESCE(SUM(amount), 0) as month_earned
            FROM salary
            WHERE emp_id = ?
            AND description LIKE 'Day Salary%'
            AND DATE_FORMAT(date, '%Y-%m') = ?";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $emp_id, $month_year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return (float)$row['month_earned'];
}

/**
 * Calculate total salary paid to employee in a specific month
 * Sums all "Salary Paid" entries from salary table
 * 
 * @param mysqli $con Database connection
 * @param int $emp_id Employee ID
 * @param string $month_year Month in 'Y-m' format (e.g., '2025-12')
 * @return float Total paid salary for the month (absolute value)
 */
function getMonthlyPaidSalary($con, $emp_id, $month_year) {
    $sql = "SELECT COALESCE(SUM(ABS(amount)), 0) as month_paid
            FROM salary
            WHERE emp_id = ?
            AND description LIKE 'Salary Paid%'
            AND DATE_FORMAT(date, '%Y-%m') = ?";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $emp_id, $month_year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return (float)$row['month_paid'];
}

/**
 * Sync employee salary to expense management system
 * Creates or updates monthly salary expense record
 * 
 * @param mysqli $con Database connection
 * @param int $emp_id Employee ID
 * @param string $emp_name Employee name
 * @param string $month_year Month in 'Y-m' format (e.g., '2025-12')
 * @return array ['expense_id' => int, 'monthly_earned' => float, 'monthly_paid' => float]
 */
function syncSalaryExpense($con, $emp_id, $emp_name, $month_year = null) {
    if ($month_year === null) {
        $month_year = date('Y-m');
    }
    
    // Get category
    $cat_id = getSalaryCategory($con);
    
    // Calculate monthly totals
    $monthly_earned = getMonthlyEarnedSalary($con, $emp_id, $month_year);
    $monthly_paid = getMonthlyPaidSalary($con, $emp_id, $month_year);
    
    // Format title: "Salary - John Doe - December 2025"
    $month_year_obj = DateTime::createFromFormat('Y-m', $month_year);
    $month_name = $month_year_obj->format('F Y');
    $expense_title = "Salary - $emp_name - $month_name";
    
    // Check if expense exists
    $exp_check = mysqli_query($con, "SELECT expense_id, amount FROM expenses 
                                     WHERE title = '$expense_title' 
                                     ORDER BY expense_id DESC LIMIT 1");
    
    if (mysqli_num_rows($exp_check) > 0) {
        // Update existing expense amount to match current monthly earned
        $exp_row = mysqli_fetch_assoc($exp_check);
        $expense_id = $exp_row['expense_id'];
        
        // Only update if amount changed
        if ((float)$exp_row['amount'] !== $monthly_earned) {
            mysqli_query($con, "UPDATE expenses 
                               SET amount = '$monthly_earned' 
                               WHERE expense_id = '$expense_id'");
        }
    } else {
        // Create new expense
        // Start with unpaid status (trigger will update based on payments)
        $ins_sql = "INSERT INTO expenses 
                    (title, amount, category_id, status, expense_date, payment_method, amount_paid, created_by) 
                    VALUES ('$expense_title', '$monthly_earned', '$cat_id', 'unpaid', NOW(), 'Cash', 0, 0)";
        mysqli_query($con, $ins_sql);
        $expense_id = mysqli_insert_id($con);
    }
    
    return [
        'expense_id' => (int)$expense_id,
        'monthly_earned' => $monthly_earned,
        'monthly_paid' => $monthly_paid
    ];
}

/**
 * Add salary payment to expense_payments table
 * Records a payment transaction and triggers auto-update of expense status
 * 
 * @param mysqli $con Database connection
 * @param int $expense_id Expense ID
 * @param float $amount Payment amount
 * @param string $payment_method Payment method (default: 'Cash')
 * @param string $notes Payment notes (default: 'Salary Payment')
 * @param int $created_by User ID who made the payment (0 = system/company)
 * @return int Payment ID
 */
function addSalaryPayment($con, $expense_id, $amount, $payment_method = 'Cash', $notes = 'Salary Payment', $created_by = 0) {
    $sql = "INSERT INTO expense_payments 
            (expense_id, payment_amount, payment_date, payment_method, notes, created_by) 
            VALUES (?, ?, NOW(), ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'idssi', $expense_id, $amount, $payment_method, $notes, $created_by);
    mysqli_stmt_execute($stmt);
    $payment_id = mysqli_insert_id($con);
    mysqli_stmt_close($stmt);
    
    return (int)$payment_id;
}
