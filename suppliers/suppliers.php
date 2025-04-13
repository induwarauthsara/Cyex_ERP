<?php
require_once '../inc/config.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add_supplier':
                $supplierName = mysqli_real_escape_string($con, $_POST['supplier_name']);
                $supplierTel = mysqli_real_escape_string($con, $_POST['supplier_tel']);
                $supplierAddress = mysqli_real_escape_string($con, $_POST['supplier_address']);
                $note = mysqli_real_escape_string($con, $_POST['note']);

                // Validate supplier name
                if (empty($supplierName)) {
                    throw new Exception("Supplier name is required");
                }

                // Check if supplier already exists
                $checkQuery = "SELECT supplier_id FROM suppliers WHERE supplier_name = '$supplierName'";
                $result = mysqli_query($con, $checkQuery);
                if (mysqli_num_rows($result) > 0) {
                    throw new Exception("Supplier with this name already exists");
                }

                $query = "INSERT INTO suppliers (supplier_name, supplier_tel, supplier_address, note) 
                         VALUES ('$supplierName', '$supplierTel', '$supplierAddress', '$note')";

                if (!insert_query($query, "Added new supplier: $supplierName", "ADD_SUPPLIER")) {
                    throw new Exception("Failed to add supplier");
                }

                echo json_encode(['status' => 'success', 'message' => 'Supplier added successfully']);
                break;

            case 'edit_supplier':
                $supplierId = (int)$_POST['supplier_id'];
                $supplierName = mysqli_real_escape_string($con, $_POST['supplier_name']);
                $supplierTel = mysqli_real_escape_string($con, $_POST['supplier_tel']);
                $supplierAddress = mysqli_real_escape_string($con, $_POST['supplier_address']);
                $note = mysqli_real_escape_string($con, $_POST['note']);

                // Validate supplier exists
                $checkQuery = "SELECT supplier_id FROM suppliers WHERE supplier_id = $supplierId";
                $result = mysqli_query($con, $checkQuery);
                if (mysqli_num_rows($result) === 0) {
                    throw new Exception("Supplier not found");
                }

                $query = "UPDATE suppliers SET 
                         supplier_name = '$supplierName',
                         supplier_tel = '$supplierTel',
                         supplier_address = '$supplierAddress',
                         note = '$note'
                         WHERE supplier_id = $supplierId";

                if (!insert_query($query, "Updated supplier: $supplierName", "EDIT_SUPPLIER")) {
                    throw new Exception("Failed to update supplier");
                }

                echo json_encode(['status' => 'success', 'message' => 'Supplier updated successfully']);
                break;

            case 'delete_supplier':
                $supplierId = (int)$_POST['supplier_id'];

                // Check if supplier has any related records
                $checkQuery = "SELECT COUNT(*) as count FROM purchase WHERE supplier = (SELECT supplier_name FROM suppliers WHERE supplier_id = $supplierId)";
                $result = mysqli_query($con, $checkQuery);
                $row = mysqli_fetch_assoc($result);

                if ($row['count'] > 0) {
                    throw new Exception("Cannot delete supplier with existing purchase records");
                }

                $query = "DELETE FROM suppliers WHERE supplier_id = $supplierId";
                if (!insert_query($query, "Deleted supplier ID: $supplierId", "DELETE_SUPPLIER")) {
                    throw new Exception("Failed to delete supplier");
                }

                echo json_encode(['status' => 'success', 'message' => 'Supplier deleted successfully']);
                break;

            case 'add_payment':
                $supplierId = (int)$_POST['supplier_id'];
                $amount = (float)$_POST['amount'];
                $method = mysqli_real_escape_string($con, $_POST['method']);
                $referenceNo = mysqli_real_escape_string($con, $_POST['reference_no']);
                $note = mysqli_real_escape_string($con, $_POST['note']);

                // Start transaction
                mysqli_begin_transaction($con);

                try {
                    // Add payment record
                    $query = "INSERT INTO supplier_payments (supplier_id, amount, method, reference_no, note, created_by) 
                             VALUES ($supplierId, $amount, '$method', '$referenceNo', '$note', $employee_id)";

                    if (!insert_query($query, "Added supplier payment: $amount", "ADD_PAYMENT")) {
                        throw new Exception("Failed to add payment");
                    }

                    // Update supplier credit balance
                    $updateQuery = "UPDATE suppliers SET credit_balance = credit_balance - $amount 
                                  WHERE supplier_id = $supplierId";

                    if (!insert_query($updateQuery, "Updated supplier credit balance", "UPDATE_CREDIT")) {
                        throw new Exception("Failed to update credit balance");
                    }
                    
                    transaction_log("ADD_PAYMENT", "Payment added for supplier ID $supplierId", $amount);

                    mysqli_commit($con);
                    echo json_encode(['status' => 'success', 'message' => 'Payment added successfully']);
                } catch (Exception $e) {
                    mysqli_rollback($con);
                    throw new Exception("Transaction failed: " . $e->getMessage());
                }
                break;

            default:
                throw new Exception("Invalid action");
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'get_suppliers':
                $query = "SELECT * FROM suppliers ORDER BY supplier_name";
                $suppliers = fetch_data($query);
                echo json_encode(['status' => 'success', 'data' => $suppliers]);
                break;

            case 'get_supplier':
                $supplierId = (int)$_GET['supplier_id'];
                $query = "SELECT * FROM suppliers WHERE supplier_id = $supplierId";
                $supplier = fetch_data($query);

                if (empty($supplier)) {
                    throw new Exception("Supplier not found");
                }

                echo json_encode(['status' => 'success', 'data' => $supplier[0]]);
                break;

            case 'get_payment_history':
                $supplierId = (int)$_GET['supplier_id'];
                $query = "SELECT sp.*, e.emp_name as created_by_name 
                         FROM supplier_payments sp 
                         LEFT JOIN employees e ON sp.created_by = e.employ_id 
                         WHERE sp.supplier_id = $supplierId 
                         ORDER BY sp.date DESC, sp.created_at DESC";
                $payments = fetch_data($query);
                echo json_encode(['status' => 'success', 'data' => $payments]);
                break;

            default:
                throw new Exception("Invalid action");
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
