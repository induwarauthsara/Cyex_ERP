<?php require_once 'config.php';
session_start(); ?>

<?php print_r($_GET);
$for = $_GET['for'];
$amount = $_GET['amount'];
$employee_name = $_SESSION['employee_name'];
$account = $_GET['account'];

// Add data to Petty Cash Tabel
$sql = "INSERT INTO pettycash (perrycash, amount, emp_name) VALUES ('{$for}','{$amount}', '{$employee_name}') ";
insert_query($sql, "for : $for , Rs. $amount, By : $employee_name", "Add Petty Cash");

// --- INTEGRATION: Expenses Module ---
// Get/Create Category
$cat_query = mysqli_query($con, "SELECT category_id FROM expense_categories WHERE category_name = 'Petty Cash'");
if(mysqli_num_rows($cat_query) > 0) {
    $cat_data = mysqli_fetch_assoc($cat_query);
    $cat_id = $cat_data['category_id'];
} else {
    mysqli_query($con, "INSERT INTO expense_categories (category_name, color_code, icon, status) VALUES ('Petty Cash', '#95a5a6', 'coins', 1)");
    $cat_id = mysqli_insert_id($con);
}

// Insert Expense (Treat as Paid immediately)
// Using 'unpaid' first then 'paid' via payment trigger is cleaner if trigger exists, 
// but direct 'paid' + amount_paid works too if we want to be explicit.
// The payment trigger updates status. So let's insert 'unpaid' then payment.
$for_escaped = mysqli_real_escape_string($con, $for);
$amount_escaped = mysqli_real_escape_string($con, $amount);

$ins_sql = "INSERT INTO expenses (title, amount, category_id, status, expense_date, payment_method, amount_paid, created_by) VALUES ('$for_escaped', '$amount_escaped', '$cat_id', 'unpaid', NOW(), 'Cash', 0, 1)";
mysqli_query($con, $ins_sql);
$expense_id = mysqli_insert_id($con);

$pay_sql = "INSERT INTO expense_payments (expense_id, payment_amount, payment_date, payment_method, notes, created_by) VALUES ('$expense_id', '$amount_escaped', NOW(), 'Cash', 'Petty Cash', 1)";
mysqli_query($con, $pay_sql);
// ------------------------------------

// Petty Cash eka Company Profit & Casg Account eken adu karanawa
$sql = "UPDATE accounts SET amount = amount - {$amount} WHERE account_name IN ('Company Profit', '$account');";
insert_query($sql,"for : $for , Rs. $amount, By : $employee_name, Account : $account and profit", "Fall Petty Cash from $account Account and profit");

// Add Transaction Log
$transaction_type = 'Petty Cash';
transaction_log($transaction_type, $for, -$amount);
?>

<?php end_db_con(); ?>