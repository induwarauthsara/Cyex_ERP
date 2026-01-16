<?php

require_once __DIR__ . '/../../../inc/config.php';

class InvoiceService {
    
    private $con;
    private $user_id;
    private $user_name;

    public function __construct($db_connection, $user_id, $user_name) {
        $this->con = $db_connection;
        $this->user_id = $user_id;
        $this->user_name = $user_name;
    }

    /**
     * Update Invoice Logic
     */
    public function updateInvoice($invoice_number, $new_items, $discount = 0, $reason = '', $restock_removed = true, $dry_run = false, $biller_name = null, $worker_name = null, $update_commission = true) {
        
        $old_invoice = $this->getInvoiceData($invoice_number);
        if (!$old_invoice) throw new Exception("Invoice not found: $invoice_number");

        // Fetch Existing OTPs for Cost Consistency
        $existing_otps = [];
        $otp_query = "SELECT * FROM oneTimeProducts_sales WHERE invoice_number = $invoice_number";
        $otp_res = mysqli_query($this->con, $otp_query);
        while($row = mysqli_fetch_assoc($otp_res)) {
            $existing_otps[$row['product_name']] = $row;
        }
        
        // Calculate Item & Stock Diffs
        list($diffs, $stockImpact) = $this->calculateDifferences($old_invoice['items'], $new_items, $restock_removed);

        // 3. Calculate Financial Impact (Profit/Commission)
        $new_structure = $this->simulateInvoiceRecalculation($old_invoice, $new_items, $discount, $existing_otps);
        $profit_diff = $new_structure['profit'] - $old_invoice['profit'];
        
        
        // Calculate Commission Diff (Per-Product Logic)
        $commission_diff = 0;
        if ($update_commission) {
            $old_commission = $this->calculateCommissionTotal($old_invoice['items'], true); // Use stored profit
            $new_commission = $this->calculateCommissionTotal($new_structure['items'], false, $existing_otps); // Calculate new profit
            $commission_diff = $new_commission - $old_commission;
        }

        // Determine effective biller/worker (Logic: Workers fetch commission)
        $eff_worker = $worker_name ?: $old_invoice['primary_worker'];
        $w_id = $this->getEmployeeId($eff_worker);
        
        $commissionImpact = [
            'worker' => ['id' => $w_id, 'amount' => $commission_diff],
            'company' => ['amount' => ($profit_diff - $commission_diff)]
        ];

        if ($dry_run) {
            return [
                'status' => 'preview',
                'item_diffs' => $diffs,
                'stock_changes' => $stockImpact,
                'financial_changes' => [
                    'old_total' => $old_invoice['total'],
                    'new_total' => $new_structure['total'],
                    'profit_diff' => $profit_diff,
                    'commission_diff' => $commission_diff
                ],
                'commission_changes' => $commissionImpact
            ];
        }

        // Execute Transaction
        mysqli_begin_transaction($this->con);
        try {
            // Apply Stock Changes
            $this->applyStockChanges($stockImpact);

            // Apply Commission Adjustments
            $this->applyCommissionChanges($commissionImpact);
            
            // ... (Rest of Update Logic Same) ... 
            
            // Update Invoice Record
            $update_query = "UPDATE invoice SET total = ?, discount = ?, balance = ?, profit = ?";
            $params = [$new_structure['total'], $discount, $new_structure['balance'], $new_structure['profit']];
            $types = "dddd";

            if ($biller_name) {
                // One Invoice - One Employee Logic. Update both columns to ensure consistency.
                $update_query .= ", biller = ?, primary_worker = ?";
                $params[] = $biller_name;
                $params[] = $biller_name;
                $types .= "ss";
            }
            // Removed separate check for worker_name as it is now redundant

            $update_query .= " WHERE invoice_number = ?";
            $params[] = $invoice_number;
            $types .= "i";

            $stmt = mysqli_prepare($this->con, $update_query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);

            // Update Sales Items & Sync OTPs
            mysqli_query($this->con, "DELETE FROM sales WHERE invoice_number = $invoice_number");
            
            $processed_otps = [];
            foreach ($new_structure['items'] as $item) {
                $item['worker'] = $worker_name ?: $old_invoice['primary_worker'];
                
                $p_name = $item['product'];
                $existing_otp_row = isset($existing_otps[$p_name]) ? $existing_otps[$p_name] : null;
                
                if($existing_otp_row) {
                    $item['cost'] = $existing_otp_row['cost'];
                    $processed_otps[$p_name] = true;
                }

                $this->insertSalesItem($invoice_number, $item, $existing_otp_row);
            }

            foreach ($existing_otps as $p_name => $row) {
                if (!isset($processed_otps[$p_name])) {
                    $this->softDeleteOneTimeProduct($row['oneTimeProduct_id']);
                }
            }

            // Sync Commission History (Add new / Mark deleted unused) - OPTIONAL but Recommended
            // For now, focusing on Balance correctness.

            $this->logAudit($invoice_number, 'EDIT', $old_invoice, $new_structure, $reason, $stockImpact, $commissionImpact, $restock_removed);

            mysqli_commit($this->con);
            return ['status' => 'success', 'message' => "Invoice updated successfully."];

        } catch (Exception $e) {
            mysqli_rollback($this->con);
            throw $e;
        }
    }
    
    
    // --- Missing Helpers Implementation ---

    private function calculateDifferences($old_items, $new_items, $restock_removed = true) {
        $diffs = [];
        $stockImpact = [];

        $new_map = [];
        foreach ($new_items as $item) {
            $name = $item['product'];
            if (!isset($new_map[$name])) $new_map[$name] = 0;
            $new_map[$name] += $item['qty'];
        }

        $old_map = [];
        foreach ($old_items as $item) {
            $name = $item['product'];
            if (!isset($old_map[$name])) $old_map[$name] = 0;
            $old_map[$name] += $item['qty'];
        }

        foreach ($old_map as $product => $old_qty) {
            $new_qty = isset($new_map[$product]) ? $new_map[$product] : 0;
            if ($new_qty < $old_qty) {
                $diff = $old_qty - $new_qty;
                if ($new_qty == 0) {
                     $diffs[] = ['type' => 'removed', 'product' => $product, 'diff' => "-$old_qty"];
                } else {
                     $diffs[] = ['type' => 'modified', 'product' => $product, 'diff' => "-$diff"];
                }
                
                if ($restock_removed && !$this->isOneTimeProduct($product)) {
                    $stockImpact[] = ['type' => 'product', 'name' => $product, 'change' => +$diff]; 
                }
            }
        }

        foreach ($new_map as $product => $new_qty) {
            $old_qty = isset($old_map[$product]) ? $old_map[$product] : 0;
            if ($new_qty > $old_qty) {
                $diff = $new_qty - $old_qty;
                if ($old_qty == 0) {
                    $diffs[] = ['type' => 'added', 'product' => $product, 'diff' => "+$new_qty"];
                } else {
                    $diffs[] = ['type' => 'modified', 'product' => $product, 'diff' => "+$diff"];
                }

                 if (!$this->isOneTimeProduct($product)) {
                    $stockImpact[] = ['type' => 'product', 'name' => $product, 'change' => -$diff];
                }
            }
        }

        return [$diffs, $stockImpact];
    }
    
    private function isOneTimeProduct($productName) {
        $esc = mysqli_real_escape_string($this->con, $productName);
        $res = mysqli_query($this->con, "SELECT product_id FROM products WHERE product_name = '$esc'");
        return (mysqli_num_rows($res) === 0);
    }

    private function simulateInvoiceRecalculation($old_invoice, $new_items, $discount, $otps) {
        $total = 0;
        $profit = 0;
        $calculated_items = [];
        
        // Map Old Costs to preserve historical consistency
        $old_costs = [];
        if (isset($old_invoice['items']) && is_array($old_invoice['items'])) {
            foreach ($old_invoice['items'] as $oi) {
                $old_costs[$oi['product']] = $oi['cost'];
            }
        }
        
        foreach ($new_items as $item) {
             $qty = $item['qty'];
             $rate = $item['rate'];
             $product = $item['product'];
             
             $cost = 0;
             if (isset($otps[$product])) {
                 $cost = $otps[$product]['cost'];
             } elseif (isset($old_costs[$product])) {
                 $cost = $old_costs[$product];
             } else {
                 $cost = $this->getProductCost($product);
             }
             
             $item_amount = $qty * $rate;
             $item_profit = ($rate - $cost) * $qty;
             
             $total += $item_amount;
             $profit += $item_profit;
             
             $item['cost'] = $cost;
             $item['profit'] = $item_profit;
             $calculated_items[] = $item;
        }
        
        $final_total = $total - $discount;
        $balance = $final_total - $old_invoice['advance']; 
        
        return [
            'total' => $final_total,
            'profit' => $profit - $discount,
            'items' => $calculated_items,
            'balance' => $balance
        ];
    }

    private function getProductCost($product) {
        $esc = mysqli_real_escape_string($this->con, $product);
        
        // 1. Try product_view (Standard)
        $q = mysqli_query($this->con, "SELECT cost FROM product_view WHERE product_name = '$esc'");
        if ($q && $row = mysqli_fetch_assoc($q)) return $row['cost'];

        // 2. Fallback: Latest active product_batch
        $q2 = mysqli_query($this->con, "SELECT pb.cost FROM product_batch pb JOIN products p ON pb.product_id = p.product_id WHERE p.product_name = '$esc' ORDER BY pb.created_at DESC LIMIT 1");
        if ($q2 && $row2 = mysqli_fetch_assoc($q2)) return $row2['cost'];

        return 0;
    }

    private function insertSalesItem($invoice_number, $item, $existing_otp) {
        $product = mysqli_real_escape_string($this->con, $item['product']);
        $qty = $item['qty'];
        $rate = $item['rate'];
        $amount = $qty * $rate;
        $cost = isset($item['cost']) ? $item['cost'] : 0;
        $profit = isset($item['profit']) ? $item['profit'] : (($rate - $cost) * $qty);
        $worker = mysqli_real_escape_string($this->con, $item['worker']);
        
        $query = "INSERT INTO sales (invoice_number, product, qty, rate, amount, cost, profit, worker, datetime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($this->con, $query);
        mysqli_stmt_bind_param($stmt, "isddddds", $invoice_number, $product, $qty, $rate, $amount, $cost, $profit, $worker);
        mysqli_stmt_execute($stmt);
        
        if (isset($item['isOneTimeProduct']) && $item['isOneTimeProduct']) {
            if ($existing_otp) {
                 $u_query = "UPDATE oneTimeProducts_sales SET qty = ?, rate = ?, amount = ?, cost = ?, profit = ?, status = 'uncleared', worker = ? WHERE oneTimeProduct_id = ?";
                 $u_stmt = mysqli_prepare($this->con, $u_query);
                 $oid = $existing_otp['oneTimeProduct_id'];
                 mysqli_stmt_bind_param($u_stmt, "dddddsi", $qty, $rate, $amount, $cost, $profit, $worker, $oid);
                 mysqli_stmt_execute($u_stmt);
            } else {
                 $i_query = "INSERT INTO oneTimeProducts_sales (invoice_number, product, qty, rate, amount, cost, profit, status, worker) VALUES (?, ?, ?, ?, ?, ?, ?, 'uncleared', ?)";
                 $i_stmt = mysqli_prepare($this->con, $i_query);
                 mysqli_stmt_bind_param($i_stmt, "isddddds", $invoice_number, $product, $qty, $rate, $amount, $cost, $profit, $worker);
                 mysqli_stmt_execute($i_stmt);
            }
        }
    }

    private function softDeleteOneTimeProduct($otp_id) {
         mysqli_query($this->con, "UPDATE oneTimeProducts_sales SET status = 'deleted' WHERE oneTimeProduct_id = $otp_id");
    }

    // --- Helper for Commission ---
    private function getCommissionSetting() {
        $res = mysqli_query($this->con, "SELECT setting_value FROM settings WHERE setting_name = 'employee_commission_enabled'");
        if ($res && $row = mysqli_fetch_assoc($res)) return (int)$row['setting_value'];
        return 0; // Default off
    }

    private function calculateCommissionTotal($items, $use_stored_profit = false, $otp_map = []) {
        if ($this->getCommissionSetting() !== 1) return 0;

        $totalComm = 0;
        foreach ($items as $item) {
            // Get Profit
            $profit = 0;
            if ($use_stored_profit && isset($item['profit'])) {
                $profit = $item['profit'];
            } else {
                $cost = 0;
                if (isset($item['cost'])) $cost = $item['cost'];
                elseif (isset($otp_map[$item['product']])) $cost = $otp_map[$item['product']]['cost'];
                else $cost = $this->getProductCost($item['product']);
                $profit = ($item['qty'] * $item['rate']) - ($item['qty'] * $cost);
            }

            // Get Pct
            $pct = 0;
            $pName = mysqli_real_escape_string($this->con, $item['product']);
            $q = mysqli_query($this->con, "SELECT employee_commission_percentage FROM products WHERE product_name = '$pName'");
            if ($q && $row = mysqli_fetch_assoc($q)) {
                 $pct = $row['employee_commission_percentage'];
            }
            
            if ($pct > 0 && $profit > 0) {
                $totalComm += ($profit * $pct / 100);
            }
        }
        return $totalComm;
    }

    // --- ... ---

    private function calculateCommissionAdjustment($invoice, $profit_diff) {
        // Deprecated - Replaced by Logic inside updateInvoice
        return [];
    }

    // ...

    /**
     * Delete Invoice Logic
     */
    public function deleteInvoice($invoice_number, $reason, $restock = true, $dry_run = false) {
        $invoice = $this->getInvoiceData($invoice_number);
        if (!$invoice) throw new Exception("Invoice #$invoice_number not found.");

        $stockImpact = $this->calculateStockRollback($invoice['items'], $restock);
        
        // Calculate Commission Reversal (Per-Product)
        $total_commission = $this->calculateCommissionTotal($invoice['items'], true);
        
        $eff_worker = $invoice['primary_worker'];
        $w_id = $this->getEmployeeId($eff_worker);
        
        $commissionImpact = [
            'worker' => ['id' => $w_id, 'amount' => -$total_commission],
            'company' => ['amount' => -($invoice['profit'] - $total_commission)]
        ];

        if ($dry_run) {
            return [
                'status' => 'preview',
                'stock_changes' => $stockImpact,
                'commission_changes' => $commissionImpact,
                'net_refund' => $invoice['total'],
                'debug_comm' => $total_commission
            ];
        }

        mysqli_begin_transaction($this->con);
        try {
            if ($restock) $this->applyStockChanges($stockImpact);
            $this->applyCommissionChanges($commissionImpact);
            
            $stmt = mysqli_prepare($this->con, "UPDATE invoice SET is_deleted = 1, deleted_at = NOW(), deleted_by = ? WHERE invoice_number = ?");
            mysqli_stmt_bind_param($stmt, 'ii', $this->user_id, $invoice_number);
            mysqli_stmt_execute($stmt);

            $stmt = mysqli_prepare($this->con, "UPDATE oneTimeProducts_sales SET status = 'deleted' WHERE invoice_number = ?");
            mysqli_stmt_bind_param($stmt, 'i', $invoice_number);
            mysqli_stmt_execute($stmt);
            
            $this->logAudit($invoice_number, 'DELETE', $invoice, null, $reason, $stockImpact, $commissionImpact, $restock);

            mysqli_commit($this->con);
            return ['status' => 'success', 'message' => "Invoice #$invoice_number deleted successfully."];

        } catch (Exception $e) {
            mysqli_rollback($this->con);
            throw $e;
        }
    }


    // --- Helper Methods ---

    private function getInvoiceData($invoice_number) {
        // Fetch Invoice Header
        $query = "SELECT * FROM invoice WHERE invoice_number = ? AND is_deleted = 0";
        $stmt = mysqli_prepare($this->con, $query);
        mysqli_stmt_bind_param($stmt, "i", $invoice_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);

        if (!$invoice) return null;

        // Fetch Items
        $items_query = "SELECT * FROM sales WHERE invoice_number = ?";
        $stmt = mysqli_prepare($this->con, $items_query);
        mysqli_stmt_bind_param($stmt, "i", $invoice_number);
        mysqli_stmt_execute($stmt);
        $items_result = mysqli_stmt_get_result($stmt);
        
        $invoice['items'] = [];
        while ($row = mysqli_fetch_assoc($items_result)) {
            $invoice['items'][] = $row;
        }

        return $invoice;
    }

    private function calculateStockRollback($items, $restock) {
        $changes = [];
        if (!$restock) return $changes;

        foreach ($items as $item) {
            // Restore Product Stock
            $changes[] = [
                'type' => 'product',
                'name' => $item['product'],
                'change' => +$item['qty']
            ];

            // Check for Raw Materials
            if (isset($item['product'])) {
               $p_name = mysqli_real_escape_string($this->con, $item['product']); 
               // Check if table exists before querying to avoid error
               $tableCheck = mysqli_query($this->con, "SHOW TABLES LIKE 'makeProduct'");
               if (mysqli_num_rows($tableCheck) > 0) {
                   $raw_q = mysqli_query($this->con, "SELECT item_name, qty FROM makeProduct WHERE product_name='$p_name'");
                   if($raw_q) {
                       while($raw = mysqli_fetch_assoc($raw_q)) {
                           $changes[] = [
                               'type' => 'raw_material',
                               'name' => $raw['item_name'],
                               'change' => +($raw['qty'] * $item['qty'])
                           ];
                       }
                   }
               }
            }
        }
        return $changes;
    }

    private function calculateCommissionReversal($invoice) {
        $profit = $invoice['profit'];
        $biller = $invoice['biller']; 
        $worker = $invoice['primary_worker']; 

        $b_id = $this->getEmployeeId($biller);
        $w_id = $this->getEmployeeId($worker);

        $changes = [];

        if ($b_id == $w_id) {
            $deduction = ($profit * 15) / 100;
            $changes['biller'] = ['id' => $b_id, 'amount' => -$deduction];
            $changes['company'] = ['amount' => -($profit - $deduction)];
        } else {
            $b_deduction = ($profit * 5) / 100;
            $w_deduction = ($profit * 10) / 100;
            
            $changes['biller'] = ['id' => $b_id, 'amount' => -$b_deduction];
            $changes['worker'] = ['id' => $w_id, 'amount' => -$w_deduction];
            $changes['company'] = ['amount' => -($profit - ($b_deduction + $w_deduction))];
        }

        return $changes;
    }

    private function getEmployeeId($identifier) {
        if (is_numeric($identifier)) return $identifier;
        $stmt = mysqli_prepare($this->con, "SELECT employ_id FROM employees WHERE emp_name = ?");
        mysqli_stmt_bind_param($stmt, "s", $identifier);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) return $row['employ_id'];
        return 0;
    }

    private function applyStockChanges($changes) {
        foreach ($changes as $change) {
            $qty = $change['change'];
            $name = mysqli_real_escape_string($this->con, $change['name']);
            
            if ($change['type'] == 'product') {
                // Get Product ID
                $p_res = mysqli_query($this->con, "SELECT product_id FROM products WHERE product_name = '$name'");
                if ($p_res && $p_row = mysqli_fetch_assoc($p_res)) {
                    $pid = $p_row['product_id'];
                    
                    if ($qty < 0) {
                         // Deduct Stock (FIFO - Oldest Batch first)
                         $b_query = "SELECT batch_id FROM product_batch WHERE product_id = $pid ORDER BY expiry_date ASC, restocked_at ASC LIMIT 1";
                    } else {
                         // Add Stock (Restock) - Add to Latest Batch
                         $b_query = "SELECT batch_id FROM product_batch WHERE product_id = $pid ORDER BY restocked_at DESC LIMIT 1";
                    }
                    
                    $b_res = mysqli_query($this->con, $b_query);
                    if ($b_res && $b_row = mysqli_fetch_assoc($b_res)) {
                        $bid = $b_row['batch_id'];
                        mysqli_query($this->con, "UPDATE product_batch SET quantity = quantity + ($qty) WHERE batch_id = $bid");
                    }
                }
            } else {
                // Raw Material / Item
                mysqli_query($this->con, "UPDATE items SET qty = qty + $qty WHERE item_name = '$name'");
            }
        }
    }

    private function applyCommissionChanges($changes) {
        foreach ($changes as $role => $data) {
            if ($role == 'company') {
                $amt = $data['amount'];
                mysqli_query($this->con, "UPDATE accounts SET amount = amount + ($amt) WHERE account_name = 'Company Profit'");
            } else {
                $eid = $data['id'];
                $amt = $data['amount'];
                if ($eid > 0) {
                     mysqli_query($this->con, "UPDATE employees SET salary = salary + ($amt) WHERE employ_id = $eid");
                }
            }
        }
    }

    private function logAudit($inv_num, $action, $old, $new, $reason, $stock_chg, $comm_chg, $restock) {
        $old_json = json_encode($old);
        $new_json = $new ? json_encode($new) : null; 
        $stock_json = json_encode($stock_chg);
        $comm_json = json_encode($comm_chg);
        
        $stmt = mysqli_prepare($this->con, "INSERT INTO invoice_audit_logs (invoice_number, action_type, old_payload, new_payload, stock_changes, commission_changes, reason, restock_items, user_id, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issssssiis", $inv_num, $action, $old_json, $new_json, $stock_json, $comm_json, $reason, $restock, $this->user_id, $this->user_name);
        mysqli_stmt_execute($stmt);
    }
}
