<?php
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token()
{
    $headers = getallheaders();
    if (
        !isset($headers['X-CSRF-Token']) ||
        !hash_equals($_SESSION['csrf_token'], $headers['X-CSRF-Token'])
    ) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid security token'
        ]);
        exit();
    }
}

function sanitize_input($data)
{
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validate_json_input()
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    return sanitize_input($data);
}

function send_json_response($success, $message, $data = null)
{
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function log_supplier_change($supplier_id, $action, $data)
{
    global $con;

    if ($action === 'delete') {
        $query = "INSERT INTO supplier_audit_log 
                 (supplier_id, action, changed_by)
                 VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'isi',
            $supplier_id,
            $action,
            $_SESSION['employee_id']
        );
    } else {
        $query = "INSERT INTO supplier_audit_log 
                 (supplier_id, action, changed_fields, new_values, changed_by)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        $fields = implode(',', array_keys($data));
        $values = implode(',', array_values($data));
        mysqli_stmt_bind_param(
            $stmt,
            'isssi',
            $supplier_id,
            $action,
            $fields,
            $values,
            $_SESSION['employee_id']
        );
    }

    mysqli_stmt_execute($stmt);
}
