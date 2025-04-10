<?php
require_once '../../inc/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the action parameter
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'open_register':
            openRegister();
            break;
            
        case 'close_register':
            closeRegister();
            break;
            
        case 'cash_out':
            cashOut();
            break;
            
        case 'get_register_summary':
            getRegisterSummary();
            break;
            
        case 'get_today_sessions':
            getTodaySessions();
            break;
            
        case 'get_register_status':
            getRegisterStatus();
            break;
            
        default:
            $response['message'] = 'Invalid action';
            break;
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Return the response as JSON
echo json_encode($response);
exit;

/**
 * Opens a new cash register session
 */
function openRegister() {
    global $con, $response;
    
    // Check if there's already an open register session
    $check_sql = "SELECT * FROM cash_register WHERE closed_at IS NULL";
    $check_result = mysqli_query($con, $check_sql);
    
    if (!$check_result) {
        $response['message'] = 'Database error when checking open sessions: ' . mysqli_error($con);
        return;
    }
    
    if (mysqli_num_rows($check_result) > 0) {
        $response['message'] = 'There is already an open cash register session';
        return;
    }
    
    // Get parameters
    $opening_balance = isset($_POST['opening_balance']) ? floatval($_POST['opening_balance']) : 0;
    $notes = mysqli_real_escape_string($con, $_POST['notes'] ?? '');
    
    // Insert new register session
    $sql = "INSERT INTO cash_register (
                opening_balance, 
                notes, 
                opened_at
            ) VALUES (
                '$opening_balance', 
                '$notes', 
                NOW()
            )";
    
    if (mysqli_query($con, $sql)) {
        $register_id = mysqli_insert_id($con);
        $response['success'] = true;
        $response['message'] = 'Cash register opened successfully';
        $response['data'] = ['register_id' => $register_id];
    } else {
        $response['message'] = 'Error opening cash register: ' . mysqli_error($con);
    }
}

/**
 * Closes a cash register session
 */
function closeRegister() {
    global $con, $response;
    
    // Get parameters
    $register_id = isset($_POST['register_id']) ? intval($_POST['register_id']) : 0;
    $actual_cash = isset($_POST['actual_cash']) ? floatval($_POST['actual_cash']) : 0;
    $bank_deposit = isset($_POST['bank_deposit']) ? floatval($_POST['bank_deposit']) : 0;
    $notes = mysqli_real_escape_string($con, $_POST['notes'] ?? '');
    
    // Debug log
    error_log("Close register request - register_id: " . $register_id . ", POST data: " . json_encode($_POST));
    
    // Validate register_id
    if ($register_id <= 0) {
        $response['message'] = 'Invalid register ID';
        error_log("Invalid register ID: " . $register_id);
        return;
    }
    
    // Get register session
    $sql = "SELECT * FROM cash_register WHERE id = $register_id AND closed_at IS NULL";
    $result = mysqli_query($con, $sql);
    
    if (!$result) {
        $response['message'] = 'Database error: ' . mysqli_error($con);
        return;
    }
    
    if (mysqli_num_rows($result) !== 1) {
        // Check if it's closed already
        $check_sql = "SELECT * FROM cash_register WHERE id = $register_id AND closed_at IS NOT NULL";
        $check_result = mysqli_query($con, $check_sql);
        
        if (mysqli_num_rows($check_result) === 1) {
            $response['message'] = 'Register session is already closed';
        } else {
            $response['message'] = 'Invalid register session or already closed';
        }
        return;
    }
    
    // Close register session
    $update_sql = "UPDATE cash_register SET 
                    actual_cash = '$actual_cash',
                    bank_deposit = '$bank_deposit',
                    closing_notes = '$notes',
                    closed_at = NOW()
                    WHERE id = $register_id";
    
    if (mysqli_query($con, $update_sql)) {
        $response['success'] = true;
        $response['message'] = 'Cash register closed successfully';
        $response['data'] = ['register_id' => $register_id];
        
        // If bank deposit is greater than 0, record it
        if ($bank_deposit > 0) {
            recordBankDeposit($bank_deposit, "Cash register bank deposit");
        }
    } else {
        $response['message'] = 'Error closing cash register: ' . mysqli_error($con);
    }
}

/**
 * Records a cash out / petty cash transaction
 */
function cashOut() {
    global $con, $response;
    
    // Get parameters
    $purpose = mysqli_real_escape_string($con, $_POST['description'] ?? '');
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $employee = mysqli_real_escape_string($con, $_SESSION['employee_name'] ?? '');
    
    // Check if there's an open register session
    $check_sql = "SELECT id FROM cash_register WHERE closed_at IS NULL ORDER BY id DESC LIMIT 1";
    $check_result = mysqli_query($con, $check_sql);
    
    if (mysqli_num_rows($check_result) === 0) {
        $response['message'] = 'No open cash register session found';
        return;
    }
    
    $register_row = mysqli_fetch_assoc($check_result);
    $register_id = $register_row['id'];
    
    // Insert cash out transaction
    $petty_sql = "INSERT INTO pettycash (
                    perrycash, 
                    amount, 
                    date, 
                    time, 
                    emp_name,
                    register_id
                ) VALUES (
                    '$purpose', 
                    '$amount', 
                    CURDATE(), 
                    CURTIME(), 
                    '$employee',
                    $register_id
                )";
    
    if (mysqli_query($con, $petty_sql)) {
        $petty_id = mysqli_insert_id($con);
        $response['success'] = true;
        $response['message'] = 'Cash out recorded successfully';
        $response['data'] = ['petty_id' => $petty_id];
    } else {
        $response['message'] = 'Error recording cash out: ' . mysqli_error($con);
    }
}

/**
 * Gets the summary of the current open register session
 */
function getRegisterSummary() {
    global $con, $response;
    
    // Get the current open register session
    $sql = "SELECT * FROM cash_register WHERE closed_at IS NULL ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con, $sql);
    
    if (mysqli_num_rows($result) === 0) {
        $response['message'] = 'No open cash register session found';
        return;
    }
    
    $register = mysqli_fetch_assoc($result);
    $register_id = $register['id'];
    $opening_balance = $register['opening_balance'];
    
    // Get total sales amount (cash, card) for the period
    // Fixing cash calculation - only count the actual bill amount, not the amount customer gave
    $sales_sql = "SELECT 
                    SUM(CASE WHEN pd.payment_method = 'Cash' THEN i.total - i.discount ELSE 0 END) as cash_sales,
                    SUM(CASE WHEN pd.payment_method = 'Card' THEN i.advance ELSE 0 END) as card_sales,
                    COUNT(*) as transaction_count
                FROM 
                    invoice i
                LEFT JOIN 
                    payment_details pd ON i.invoice_number = pd.invoice_id
                WHERE 
                    i.invoice_date = CURDATE() AND
                    i.time BETWEEN 
                        (SELECT DATE_FORMAT(opened_at, '%H:%i:%s') FROM cash_register WHERE id = $register_id) 
                    AND 
                        COALESCE(
                            (SELECT DATE_FORMAT(closed_at, '%H:%i:%s') FROM cash_register WHERE id = $register_id),
                            CURRENT_TIME()
                        )";
    
    $sales_result = mysqli_query($con, $sales_sql);
    $sales = mysqli_fetch_assoc($sales_result);
    
    $cash_sales = $sales['cash_sales'] ?? 0;
    $card_sales = $sales['card_sales'] ?? 0;
    $transaction_count = $sales['transaction_count'] ?? 0;
    
    // Get petty cash / cash out transactions
    $petty_sql = "SELECT SUM(amount) as total_cash_out FROM pettycash WHERE register_id = $register_id";
    $petty_result = mysqli_query($con, $petty_sql);
    $petty = mysqli_fetch_assoc($petty_result);
    $cash_out = $petty['total_cash_out'] ?? 0;
    
    // Calculate expected cash
    $expected_cash = $opening_balance + $cash_sales - $cash_out;
    
    // Prepare response data
    $summary = [
        'register_id' => intval($register_id),
        'opening_balance' => floatval($opening_balance),
        'cash_sales' => floatval($cash_sales),
        'card_sales' => floatval($card_sales),
        'total_sales' => floatval($cash_sales + $card_sales),
        'transaction_count' => intval($transaction_count),
        'cash_out' => floatval($cash_out),
        'expected_cash' => floatval($expected_cash)
    ];
    
    $response['success'] = true;
    $response['message'] = 'Register summary retrieved';
    $response['data'] = $summary;
}

/**
 * Gets all cash register sessions for today
 */
function getTodaySessions() {
    global $con, $response;
    
    $sql = "SELECT * FROM cash_register WHERE DATE(opened_at) = CURDATE() ORDER BY id DESC";
    $result = mysqli_query($con, $sql);
    
    $sessions = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sessions[] = $row;
        }
    }
    
    $response['success'] = true;
    $response['data'] = $sessions;
}

/**
 * Records a bank deposit
 */
function recordBankDeposit($amount, $notes) {
    global $con;
    
    $notes = mysqli_real_escape_string($con, $notes);
    
    $sql = "INSERT INTO bank_deposits (
                amount, 
                deposit_date, 
                deposit_time, 
                notes
            ) VALUES (
                '$amount', 
                CURDATE(), 
                CURTIME(), 
                '$notes'
            )";
    
    return mysqli_query($con, $sql);
}

/**
 * Gets the current register status
 */
function getRegisterStatus() {
    global $con, $response;
    
    // Check if there's an open register
    $sql = "SELECT * FROM cash_register WHERE DATE(opened_at) = CURDATE() AND closed_at IS NULL ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con, $sql);
    $is_open = mysqli_num_rows($result) > 0;
    
    $register_data = [
        'is_open' => $is_open,
        'today_date' => date('Y-m-d')
    ];
    
    if ($is_open) {
        $register = mysqli_fetch_assoc($result);
        $register_id = $register['id'];
        $opening_balance = $register['opening_balance'];
        
        // Get total sales amount (cash, card) for the period
        // Fixing cash calculation - only count the actual bill amount, not the amount customer gave
        $sales_sql = "SELECT 
                        SUM(CASE WHEN pd.payment_method = 'Cash' THEN i.total - i.discount ELSE 0 END) as cash_sales,
                        SUM(CASE WHEN pd.payment_method = 'Card' THEN i.advance ELSE 0 END) as card_sales,
                        COUNT(*) as transaction_count
                    FROM 
                        invoice i
                    LEFT JOIN 
                        payment_details pd ON i.invoice_number = pd.invoice_id
                    WHERE 
                        i.invoice_date = CURDATE() AND
                        i.time BETWEEN 
                            (SELECT DATE_FORMAT(opened_at, '%H:%i:%s') FROM cash_register WHERE id = $register_id) 
                        AND 
                            CURRENT_TIME()";
        
        $sales_result = mysqli_query($con, $sales_sql);
        $sales = mysqli_fetch_assoc($sales_result);
        
        $cash_sales = $sales['cash_sales'] ?? 0;
        $card_sales = $sales['card_sales'] ?? 0;
        $transaction_count = $sales['transaction_count'] ?? 0;
        
        // Get petty cash / cash out transactions
        $petty_sql = "SELECT SUM(amount) as total_cash_out FROM pettycash WHERE register_id = $register_id";
        $petty_result = mysqli_query($con, $petty_sql);
        $petty = mysqli_fetch_assoc($petty_result);
        $cash_out = $petty['total_cash_out'] ?? 0;
        
        // Calculate expected cash
        $expected_cash = $opening_balance + $cash_sales - $cash_out;
        
        $register_data['register_id'] = intval($register_id);
        $register_data['details'] = [
            'opening_balance' => floatval($opening_balance),
            'total_sales' => floatval($cash_sales + $card_sales),
            'cash_sales' => floatval($cash_sales),
            'card_sales' => floatval($card_sales),
            'transaction_count' => intval($transaction_count),
            'cash_out' => floatval($cash_out),
            'expected_cash' => floatval($expected_cash)
        ];
    }
    
    $response['success'] = true;
    $response['data'] = $register_data;
} 